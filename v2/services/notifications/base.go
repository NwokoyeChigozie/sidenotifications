package notifications

import (
	"github.com/vesicash/notifications-ms/v2/external/request"
	"github.com/vesicash/notifications-ms/v2/pkg/repository/storage/postgresql"
	"github.com/vesicash/notifications-ms/v2/services/names"
)

type NotificationObject struct {
	Name   names.NotificationName
	ExtReq request.ExternalRequest
	Db     postgresql.Databases
	Data   interface{}
}
