package migrations

import "github.com/vesicash/notifications-ms/v2/internal/models"

// _ = db.AutoMigrate(MigrationModels()...)
func NotificationsMigrationModels() []interface{} {
	return []interface{}{
		models.NotificationRecord{},
	}
}
