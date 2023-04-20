package upload_mocks

import (
	"bytes"
	"fmt"
	"mime/multipart"

	"github.com/vesicash/notifications-ms/v2/external/external_models"
	"github.com/vesicash/notifications-ms/v2/utility"
)

func UploadFile(logger *utility.Logger, idata interface{}) (external_models.UploadFileResponseData, error) {

	var (
		outBoundResponse external_models.UploadFileResponse
	)

	data, ok := idata.(external_models.UploadFileRequest)
	if !ok {
		logger.Error("upload one file", "request data format error")
		return external_models.UploadFileResponseData{}, fmt.Errorf("request data format error")
	}

	requestBody := new(bytes.Buffer)
	writer := multipart.NewWriter(requestBody)
	defer writer.Close()

	part, err := writer.CreateFormFile("files", data.PlaceHolderName)
	if err != nil {
		logger.Error("upload one file", outBoundResponse, err.Error())
		return external_models.UploadFileResponseData{}, err
	}
	if _, err := part.Write(data.File); err != nil {
		logger.Error("upload one file", outBoundResponse, err.Error())
		return external_models.UploadFileResponseData{}, err
	}

	// RequestBody := requestBody

	// logger.Info("upload one file", RequestBody)

	return external_models.UploadFileResponseData{
		OriginalName: "testfile.png",
		FileUrl:      "https://link.to.file",
	}, nil
}
