package test_notifications

import (
	"bytes"
	"encoding/json"
	"fmt"
	"net/http"
	"net/http/httptest"
	"net/url"
	"testing"

	"github.com/gin-gonic/gin"
	"github.com/go-playground/validator/v10"
	"github.com/gofrs/uuid"
	"github.com/vesicash/notifications-ms/external/external_models"
	"github.com/vesicash/notifications-ms/external/mocks/auth_mocks"
	"github.com/vesicash/notifications-ms/external/mocks/payment_mocks"
	"github.com/vesicash/notifications-ms/external/mocks/transactions_mocks"
	"github.com/vesicash/notifications-ms/external/request"
	"github.com/vesicash/notifications-ms/pkg/controller/notifications"
	"github.com/vesicash/notifications-ms/pkg/middleware"
	"github.com/vesicash/notifications-ms/pkg/repository/storage/postgresql"
	tst "github.com/vesicash/notifications-ms/tests"
	"github.com/vesicash/notifications-ms/utility"
)

func TestSendNotifications(t *testing.T) {
	logger := tst.Setup()
	gin.SetMode(gin.TestMode)
	validatorRef := validator.New()
	db := postgresql.Connection()
	var (
		muuid, _  = uuid.NewV4()
		accountID = uint(utility.GetRandomNumbersInRange(1000000000, 9999999999))
		testUser  = external_models.User{
			ID:           uint(utility.GetRandomNumbersInRange(1000000000, 9999999999)),
			AccountID:    accountID,
			EmailAddress: fmt.Sprintf("testuser%v@qa.team", muuid.String()),
			PhoneNumber:  fmt.Sprintf("+234%v", utility.GetRandomNumbersInRange(7000000000, 9099999999)),
			AccountType:  "individual",
			Firstname:    "test",
			Lastname:     "user",
			Username:     fmt.Sprintf("test_username%v", muuid.String()),
		}
	)

	auth_mocks.User = &testUser
	auth_mocks.BusinessProfile = &external_models.BusinessProfile{
		ID:        uint(utility.GetRandomNumbersInRange(1000000000, 9999999999)),
		AccountID: int(testUser.AccountID),
		Country:   "NG",
		Currency:  "NGN",
	}
	auth_mocks.ValidateAuthorizationRes = &external_models.ValidateAuthorizationDataModel{
		Status:  true,
		Message: "authorized",
		Data:    testUser,
	}
	auth_mocks.UserProfile = &external_models.UserProfile{
		ID:        uint(utility.GetRandomNumbersInRange(1000000000, 9999999999)),
		AccountID: int(testUser.AccountID),
		Country:   "NG",
		Currency:  "NGN",
	}

	auth_mocks.BusinessCharge = &external_models.BusinessCharge{
		ID:                  uint(utility.GetRandomNumbersInRange(1000000000, 9999999999)),
		BusinessId:          int(testUser.AccountID),
		Country:             "NG",
		Currency:            "NGN",
		BusinessCharge:      "0",
		VesicashCharge:      "2.5",
		ProcessingFee:       "0",
		PaymentGateway:      "rave",
		DisbursementGateway: "rave_momo",
		ProcessingFeeMode:   "fixed",
	}

	auth_mocks.Country = &external_models.Country{
		ID:           uint(utility.GetRandomNumbersInRange(1000000000, 9999999999)),
		Name:         "nigeria",
		CountryCode:  "NG",
		CurrencyCode: "NGN",
	}

	auth_mocks.BankDetail = &external_models.BankDetail{
		ID:          uint(utility.GetRandomNumbersInRange(1000000000, 9999999999)),
		AccountID:   int(testUser.AccountID),
		BankID:      1,
		AccountName: "test",
		AccountNo:   "79891393",
		BankName:    "vesicash bank",
		Country:     "NG",
		Currency:    "NGN",
	}

	var (
		transactionID = utility.RandomString(20)
		partiesID     = utility.RandomString(20)
		milestoneID   = utility.RandomString(20)
	)

	transactionMilestones := external_models.TransactionByID{
		ID:               uint(utility.GetRandomNumbersInRange(1000000000, 9999999999)),
		TransactionID:    transactionID,
		PartiesID:        partiesID,
		MilestoneID:      milestoneID,
		Title:            "title",
		Type:             "milestone",
		Description:      "description",
		Amount:           1000,
		Status:           "draft",
		Quantity:         1,
		InspectionPeriod: "2023-03-10",
		DueDate:          "1678924800",
		ShippingFee:      0,
		GracePeriod:      "1679097600",
		Currency:         "NGN",
		BusinessID:       int(testUser.AccountID),
		IsPaylinked:      false,
		Source:           "api",
		TransUssdCode:    74950,
		Recipients: []external_models.MileStoneRecipient{
			{
				AccountID:    9489042479,
				Amount:       500,
				EmailAddress: "test.qa.team",
				PhoneNumber:  "+23456789776789",
			},
		},
		EscrowWallet: "yes",
		Parties: map[string]external_models.TransactionParty{
			"buyer": {
				ID:                   uint(utility.GetRandomNumbersInRange(1000000000, 9999999999)),
				TransactionPartiesID: partiesID,
				TransactionID:        transactionID,
				AccountID:            int(testUser.AccountID),
				Role:                 "buyer",
				Status:               "draft",
				RoleCapabilities: &map[string]interface{}{"approve": true,
					"can_receive":  false,
					"can_view":     true,
					"mark_as_done": false,
				},
			},
			"seller": {
				ID:                   uint(utility.GetRandomNumbersInRange(1000000000, 9999999999)),
				TransactionPartiesID: partiesID,
				TransactionID:        transactionID,
				AccountID:            int(testUser.AccountID),
				Role:                 "seller",
				Status:               "draft",
				RoleCapabilities: &map[string]interface{}{"approve": true,
					"can_receive":  false,
					"can_view":     true,
					"mark_as_done": false,
				},
			},
		},
		Members: []external_models.PartyResponse{
			{
				PartyID:     utility.GetRandomNumbersInRange(1000000000, 9999999999),
				AccountID:   int(testUser.AccountID),
				AccountName: "name",
				Email:       "email@email.com",
				PhoneNumber: "+2340905964039",
				Role:        "buyer",
				Status:      "draft",
				AccessLevel: external_models.PartyAccessLevel{
					CanView:    true,
					CanReceive: false,
					MarkAsDone: false,
					Approve:    true,
				},
			},
			{
				PartyID:     utility.GetRandomNumbersInRange(1000000000, 9999999999),
				AccountID:   int(testUser.AccountID),
				AccountName: "name",
				Email:       "email@email.com",
				PhoneNumber: "+2340905964039",
				Role:        "seller",
				Status:      "draft",
				AccessLevel: external_models.PartyAccessLevel{
					CanView:    true,
					CanReceive: false,
					MarkAsDone: false,
					Approve:    true,
				},
			},
		},
		TotalAmount: 2000,
		Milestones: []external_models.MilestonesResponse{
			{
				Index:            1,
				MilestoneID:      utility.RandomString(20),
				Title:            "milestone title",
				Amount:           1000,
				Status:           "draft",
				InspectionPeriod: "2023-03-10",
				DueDate:          "1678924800",
				Recipients: []external_models.MilestonesRecipientResponse{
					{
						AccountID:   9489042479,
						AccountName: "name",
						Amount:      500,
						Email:       "test.qa.team",
						PhoneNumber: "+23456789776789",
					},
				},
			},
			{
				Index:            2,
				MilestoneID:      utility.RandomString(20),
				Title:            "milestone title 1",
				Amount:           1000,
				Status:           "draft",
				InspectionPeriod: "2023-03-10",
				DueDate:          "1678924800",
				Recipients: []external_models.MilestonesRecipientResponse{
					{
						AccountID:   9489042479,
						AccountName: "name",
						Amount:      500,
						Email:       "test.qa.team",
						PhoneNumber: "+23456789776789",
					},
				},
			},
		},
		IsDisputed: false,
	}

	transactionOneOff := external_models.TransactionByID{
		ID:               uint(utility.GetRandomNumbersInRange(1000000000, 9999999999)),
		TransactionID:    transactionID,
		PartiesID:        partiesID,
		MilestoneID:      milestoneID,
		Title:            "title",
		Type:             "oneoff",
		Description:      "description",
		Amount:           1000,
		Status:           "draft",
		Quantity:         1,
		InspectionPeriod: "2023-03-10",
		DueDate:          "1678924800",
		ShippingFee:      0,
		GracePeriod:      "1679097600",
		Currency:         "NGN",
		BusinessID:       int(testUser.AccountID),
		IsPaylinked:      false,
		Source:           "api",
		TransUssdCode:    74950,
		Recipients: []external_models.MileStoneRecipient{
			{
				AccountID:    9489042479,
				Amount:       500,
				EmailAddress: "test.qa.team",
				PhoneNumber:  "+23456789776789",
			},
		},
		EscrowWallet: "yes",
		Parties: map[string]external_models.TransactionParty{
			"buyer": {
				ID:                   uint(utility.GetRandomNumbersInRange(1000000000, 9999999999)),
				TransactionPartiesID: partiesID,
				TransactionID:        transactionID,
				AccountID:            int(testUser.AccountID),
				Role:                 "buyer",
				Status:               "draft",
				RoleCapabilities: &map[string]interface{}{"approve": true,
					"can_receive":  false,
					"can_view":     true,
					"mark_as_done": false,
				},
			},
			"seller": {
				ID:                   uint(utility.GetRandomNumbersInRange(1000000000, 9999999999)),
				TransactionPartiesID: partiesID,
				TransactionID:        transactionID,
				AccountID:            int(testUser.AccountID),
				Role:                 "seller",
				Status:               "draft",
				RoleCapabilities: &map[string]interface{}{"approve": true,
					"can_receive":  false,
					"can_view":     true,
					"mark_as_done": false,
				},
			},
		},
		Members: []external_models.PartyResponse{
			{
				PartyID:     utility.GetRandomNumbersInRange(1000000000, 9999999999),
				AccountID:   int(testUser.AccountID),
				AccountName: "name",
				Email:       "email@email.com",
				PhoneNumber: "+2340905964039",
				Role:        "buyer",
				Status:      "draft",
				AccessLevel: external_models.PartyAccessLevel{
					CanView:    true,
					CanReceive: false,
					MarkAsDone: false,
					Approve:    true,
				},
			},
			{
				PartyID:     utility.GetRandomNumbersInRange(1000000000, 9999999999),
				AccountID:   int(testUser.AccountID),
				AccountName: "name",
				Email:       "email@email.com",
				PhoneNumber: "+2340905964039",
				Role:        "seller",
				Status:      "draft",
				AccessLevel: external_models.PartyAccessLevel{
					CanView:    true,
					CanReceive: false,
					MarkAsDone: false,
					Approve:    true,
				},
			},
		},
		TotalAmount: 1000,
		Milestones: []external_models.MilestonesResponse{
			{
				Index:            1,
				MilestoneID:      utility.RandomString(20),
				Title:            "milestone title",
				Amount:           1000,
				Status:           "draft",
				InspectionPeriod: "2023-03-10",
				DueDate:          "1678924800",
				Recipients: []external_models.MilestonesRecipientResponse{
					{
						AccountID:   9489042479,
						AccountName: "name",
						Amount:      500,
						Email:       "test.qa.team",
						PhoneNumber: "+23456789776789",
					},
				},
			},
		},
		IsDisputed: false,
	}

	payment_mocks.Payment = &external_models.Payment{
		ID:               int64(utility.GetRandomNumbersInRange(1000000000, 9999999999)),
		PaymentID:        utility.RandomString(20),
		TransactionID:    transactionID,
		TotalAmount:      3000,
		EscrowCharge:     10,
		IsPaid:           false,
		AccountID:        int64(testUser.AccountID),
		Currency:         "NGN",
		ShippingFee:      20,
		DisburseCurrency: "NGN",
	}

	notificationsCtrl := notifications.Controller{Db: db, Validator: validatorRef, Logger: logger, ExtReq: request.ExternalRequest{
		Logger: logger,
		Test:   true,
	}}
	r := gin.Default()

	tests, err := GetTestCases(testUser, transactionMilestones, *payment_mocks.Payment)
	if err != nil {
		t.Fatal(err)
	}

	if len(tests) < 1 {
		t.Fatal("no notification found")
	}

	paymentApiUrl := r.Group(fmt.Sprintf("%v", "v2"), middleware.Authorize(db, notificationsCtrl.ExtReq, middleware.ApiType))
	{
		paymentApiUrl.POST("/send/:name", notificationsCtrl.SendNotification)
	}

	testMap := map[string]bool{}

	for _, test := range tests {
		if test.Name == "" {
			continue
		}
		testMap[test.Name] = true

		t.Run(test.Name, func(t *testing.T) {
			fmt.Println(test.RequestBody)

			if test.TransactionType == "oneoff" {
				transactions_mocks.ListTransactionsByIDObj = &transactionOneOff
			} else {
				transactions_mocks.ListTransactionsByIDObj = &transactionMilestones
			}

			if test.AccountType != "" {
				testUser.AccountType = test.AccountType
				auth_mocks.User = &testUser
			} else {
				testUser.AccountType = "individual"
				auth_mocks.User = &testUser
			}

			if test.TransactionSource != "" {
				transactions_mocks.ListTransactionsByIDObj.Source = test.TransactionSource
			}

			if test.BusinessType != "" {
				auth_mocks.BusinessProfile.BusinessType = test.BusinessType
			} else {
				auth_mocks.BusinessProfile.BusinessType = ""
			}

			// start sending request
			var b bytes.Buffer
			json.NewEncoder(&b).Encode(test.RequestBody)
			URI := url.URL{Path: "/v2/send/" + test.Name}

			req, err := http.NewRequest(http.MethodPost, URI.String(), &b)
			if err != nil {
				t.Fatal(err)
			}

			for i, v := range test.Headers {
				req.Header.Set(i, v)
			}

			rr := httptest.NewRecorder()
			r.ServeHTTP(rr, req)

			tst.AssertStatusCode(t, rr.Code, test.ExpectedCode)

			data := tst.ParseResponse(rr)
			fmt.Println(data)

			codefloat, ok := data["code"].(float64)
			if !ok {
				t.Errorf("invalid code %v", data)
			}
			code := int(codefloat)
			tst.AssertStatusCode(t, code, test.ExpectedCode)

			if test.Message != "" {
				message := data["message"]
				if message != nil {
					tst.AssertResponseMessage(t, message.(string), test.Message)
				} else {
					tst.AssertResponseMessage(t, "", test.Message)
				}

			}

		})

	}

	logger.Info(fmt.Sprintf("test count %v", len(tests)))
	logger.Info(fmt.Sprintf("length of tested notifications %v", len(testMap)))
	logger.Info(fmt.Sprintf("tested notifications %v", testMap))

}
