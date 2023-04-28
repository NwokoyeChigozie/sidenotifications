package migrations

import "github.com/vesicash/notifications-ms/internal/models"

// _ = db.AutoMigrate(MigrationModels()...)
func NotificationsMigrationModels() []interface{} {
	return []interface{}{
		models.NotificationRecord{},
	}
}
