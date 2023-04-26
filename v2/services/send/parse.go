package send

import (
	"bytes"
	"fmt"
	"html/template"
	"os"
	"strconv"
	template2 "text/template"
	"time"

	"github.com/pkg/errors"
	"github.com/vesicash/notifications-ms/v2/external/external_models"
	"github.com/vesicash/notifications-ms/v2/external/request"
	"github.com/vesicash/notifications-ms/v2/internal/config"
	"github.com/vesicash/notifications-ms/v2/utility"
)

func ParseTemplate(extReq request.ExternalRequest, templateFileName, baseTemplateFileName string, templateData map[string]interface{}) (string, error) {
	var (
		outputBuffer bytes.Buffer
		t            *template.Template
	)
	templateData = AddMoreMailTemplateData(extReq, templateData)

	fileName, err := utility.FindTemplateFilePath(templateFileName, "/email")
	if err != nil {
		return "", err
	}

	if baseTemplateFileName != "" {
		baseFileName, err := utility.FindTemplateFilePath(baseTemplateFileName, "/email")
		if err != nil {
			return "", err
		}

		base, err := os.ReadFile(baseFileName)
		if err != nil {
			return "", err
		}
		t = template.New("base")
		t, err = t.Parse(string(base))
		if err != nil {
			return "", err
		}
		t, err = t.ParseFiles(fileName)
		if err != nil {
			return "", err
		}

	} else {
		filedata, err := os.ReadFile(fileName)
		if err != nil {
			return "", errors.Wrap(err, "template not found")
		}

		t, err = template.New("email_template").Parse(string(filedata))
		if err != nil {
			return "", err
		}
	}

	if err2 := t.Execute(&outputBuffer, templateData); err2 != nil {
		return "", err2
	}

	body := outputBuffer.String()

	return body, nil
}

func AddMoreMailTemplateData(extReq request.ExternalRequest, data map[string]interface{}) map[string]interface{} {
	appConfig := config.GetConfig()
	var accountID int
	accountIDfloat, ok := data["account_id"].(float64)
	if !ok {
		accountIDStr, ok := data["account_id"].(string)
		if ok {
			accountID, _ = (strconv.Atoi(accountIDStr))
		}
	} else {
		accountID = int(accountIDfloat)
	}
	data["year"] = time.Now().Year()
	data["faq"] = appConfig.App.SiteUrl + "/faq"
	if accountID != 0 {
		data["dashboard"] = fmt.Sprintf("%v/login?account-id=%v", appConfig.App.SiteUrl, accountID)
	} else {
		data["dashboard"] = fmt.Sprintf("%v/login", appConfig.App.SiteUrl)
	}

	data["business_logo_uri"] = ""

	if accountID != 0 {
		businessProfileInterface, err := extReq.SendExternalRequest(request.GetBusinessProfile, external_models.GetBusinessProfileModel{
			AccountID: uint(accountID),
		})
		if err == nil {
			businessProfile, ok := businessProfileInterface.(external_models.BusinessProfile)
			if ok {
				data["business_logo_uri"] = businessProfile.LogoUri
			}
		}

	}

	return data
}

func ParseSMSTemplate(templateFileName string, templateData map[string]interface{}) (string, error) {

	fileName, err := utility.FindTemplateFilePath(templateFileName, "/sms")
	if err != nil {
		return "", err
	}

	filedata, er := os.ReadFile(fileName)
	if er != nil {
		return "", errors.Wrap(er, "template not found")
	}

	compl := string(filedata)

	t, err1 := template2.New("sms_template").Parse(compl)
	if err1 != nil {
		return "", err1
	}

	buf := new(bytes.Buffer)
	if err2 := t.Execute(buf, templateData); err2 != nil {
		return "", err2
	}

	body := buf.String()

	return body, nil
}
