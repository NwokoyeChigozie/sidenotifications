package models

import (
	"fmt"
	"time"

	"github.com/vesicash/notifications-ms/v2/pkg/repository/storage/postgresql"
	"gorm.io/gorm"
)

type NotificationRecord struct {
	ID           uint      `gorm:"column:id; type:uint; not null; primaryKey; unique; autoIncrement" json:"id"`
	Name         string    `gorm:"column:name; type:varchar(255); not null; comment: name of the notification" json:"name"`
	Data         string    `gorm:"column:data; type:text; comment: " json:"data"`
	Attempts     int       `gorm:"column:attempts; type:int; not null; default:0" json:"attempts"`
	Sent         bool      `gorm:"column:sent; type:bool; not null; default:false" json:"sent"`
	Abandoned    bool      `gorm:"column:abandoned; type:bool; not null; default:false" json:"abandoned"`
	AttemptAgain int       `gorm:"column:attempt_again; type:int;default:0" json:"attempt_again"`
	CreatedAt    time.Time `gorm:"column:created_at; autoCreateTime" json:"created_at"`
	UpdatedAt    time.Time `gorm:"column:updated_at; autoUpdateTime" json:"updated_at"`
}

func (n *NotificationRecord) GetSomeUnsentNotifications(db *gorm.DB, limit int) ([]NotificationRecord, error) {
	details := []NotificationRecord{}
	err := postgresql.SelectAllFromDbWithLimit(db, "asc", limit, &details, "sent=? and abandoned=? and attempt_again>0 and attempt_again<=?", false, false, int(time.Now().Unix()))
	if err != nil {
		return details, err
	}
	return details, nil
}

func (n *NotificationRecord) CreateNotificationRecord(db *gorm.DB) error {
	err := postgresql.CreateOneRecord(db, &n)
	if err != nil {
		return fmt.Errorf("notification record creation failed: %v", err.Error())
	}
	return nil
}

func (n *NotificationRecord) UpdateAllFields(db *gorm.DB) error {
	_, err := postgresql.SaveAllFields(db, &n)
	return err
}
