package actions

import (
	"fmt"

	"github.com/vesicash/notifications-ms/v2/external/request"
	"github.com/vesicash/notifications-ms/v2/pkg/repository/storage/postgresql"
	"github.com/vesicash/notifications-ms/v2/services/names"
	"github.com/vesicash/notifications-ms/v2/services/notifications"
)

func Send(extReq request.ExternalRequest, db postgresql.Databases, data interface{}, name names.NotificationName) error {
	var (
		err error
		req = notifications.NotificationObject{
			Name:   name,
			ExtReq: extReq,
			Db:     db,
			Data:   data,
		}
	)

	switch name {
	case names.SendWelcomeMail:
		err = req.SendWelcomeMail()
	default:
		return fmt.Errorf("send for %v, not implemented", name)
	}

	if err != nil {
		extReq.Logger.Error(fmt.Sprintf("sending %v failed", name))
	} else {
		extReq.Logger.Info(fmt.Sprintf("sending %v successful", name))
	}
	return err
}
