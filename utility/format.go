package utility

import (
	"strconv"
	"time"
)

func FormatDate(date, currentISOFormat, newISOFormat string) (string, error) {
	t, err := time.Parse(currentISOFormat, date)
	if err != nil {
		return date, err
	}
	return t.Format(newISOFormat), nil
}

func FormatInspectionPeriod(t interface{}) string {
	timeStampStr, ok := t.(string)
	if !ok {
		return ""
	}

	timeStamp, err := strconv.Atoi(timeStampStr)
	if err != nil {
		return ""
	}

	inspectionTime := time.Unix(int64(timeStamp), 0)
	return inspectionTime.Format("2006-01-02 15:04:05")
}
