package test_notifications

import (
	"net/http"
	"sync"

	"github.com/vesicash/notifications-ms/utility"
)

type TestRequest struct {
	Name              string
	RequestBody       map[string]interface{}
	RequiredFields    []string
	ExpectedCode      int
	Headers           map[string]string
	AccountType       string
	TransactionType   string
	TransactionSource string
	BusinessType      string
	Message           string
}

func NewTestObj() TestRequest {
	return TestRequest{
		ExpectedCode: http.StatusOK,
		Headers: map[string]string{
			"Content-Type":  "application/json",
			"v-private-key": utility.RandomString(20),
			"v-public-key":  utility.RandomString(20),
		},
		Message: "successful",
	}
}

func GetSideCases(test TestRequest) []TestRequest {
	var (
		tests              = []TestRequest{}
		transactionTypes   = []string{"oneoff", "milestone"}
		accountTypes       = []string{"business", "individual", "others"}
		transactionSources = []string{"instantescrow", "transfer", ""}
		businessTypes      = []string{"marketplace", "social_commerce", ""}
	)
	//creating ok requests
	tests = append(tests, test)
	tests = append(tests, GenerateAllVaryingCombinations(test, transactionTypes, accountTypes, transactionSources, businessTypes)...)
	tests = append(tests, GenerateRequestsForNonRequired(test)...)

	// creating error 400 requests
	tests = append(tests, GenerateRequestsForRequired(test)...)

	return tests
}

func GenerateRequestsForRequired(test TestRequest) []TestRequest {
	var (
		testRequests   []TestRequest
		code           = http.StatusBadRequest
		requestBody    = NewMap(test.RequestBody)
		requiredFields = test.RequiredFields
	)

	for i, _ := range requestBody {
		newRequestBody := requestBody
		if utility.InStringSlice(i, requiredFields) {
			delete(newRequestBody, i)
		}
		newTest := test
		newTest.RequestBody = newRequestBody
		newTest.ExpectedCode = code
		newTest.Message = ""
		testRequests = append(testRequests, newTest)
	}

	if len(requiredFields) > 0 {
		newRequestBody := requestBody
		for i, _ := range newRequestBody {
			if utility.InStringSlice(i, requiredFields) {
				delete(newRequestBody, i)
			}
		}

		newTest := test
		newTest.RequestBody = newRequestBody
		newTest.ExpectedCode = code
		newTest.Message = ""
		testRequests = append(testRequests, newTest)
	}

	return testRequests
}
func GenerateRequestsForNonRequired(test TestRequest) []TestRequest {
	var (
		testRequests   []TestRequest
		requestBody    = NewMap(test.RequestBody)
		requiredFields = test.RequiredFields
	)

	for i, _ := range requestBody {
		newRequestBody := requestBody
		if !utility.InStringSlice(i, requiredFields) {
			delete(newRequestBody, i)
		}
		newTest := test
		newTest.RequestBody = newRequestBody
		testRequests = append(testRequests, newTest)
	}

	if len(requiredFields) > 0 {
		newRequestBody := requestBody
		for i, _ := range newRequestBody {
			if !utility.InStringSlice(i, requiredFields) {
				delete(newRequestBody, i)
			}
		}

		newTest := test
		newTest.RequestBody = newRequestBody
		testRequests = append(testRequests, newTest)
	}

	return testRequests
}

func GenerateAllVaryingCombinations(test TestRequest, transactionTypes, accountTypes, transactionSources, businessTypes []string) []TestRequest {
	var testRequests []TestRequest

	var wg sync.WaitGroup
	for _, tt := range transactionTypes {
		for _, at := range accountTypes {
			for _, ts := range transactionSources {
				for _, bt := range businessTypes {
					wg.Add(1)
					go func(test TestRequest, tt, at, ts, bt string) {
						defer wg.Done()
						newTest := test
						newTest.AccountType = at
						newTest.TransactionType = tt
						newTest.TransactionSource = ts
						newTest.BusinessType = bt
						testRequests = append(testRequests, newTest)
					}(test, tt, at, ts, bt)
				}
			}
		}
	}
	wg.Wait()

	return testRequests
}

func NewMap(originalMap map[string]interface{}) map[string]interface{} {
	newMap := map[string]interface{}{}
	for key, value := range originalMap {
		newMap[key] = value
	}
	return newMap
}
