package actions

import (
	"fmt"

	"github.com/vesicash/notifications-ms/v2/internal/models"
	"github.com/vesicash/notifications-ms/v2/services/names"
)

func Model(name names.NotificationName) (interface{}, error) {
	switch name {
	case names.SendWelcomeMail:
		return models.SendWelcomeMail{}, nil
	default:
		return nil, fmt.Errorf("model for %v, not implemented", name)
	}
}
