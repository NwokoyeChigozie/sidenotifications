package config

import (
	"log"

	"github.com/spf13/viper"
	"github.com/vesicash/notifications-ms/utility"
)

// Setup initialize configuration
var (
	// Params ParamsConfiguration
	Config *Configuration
)

// Params = getConfig.Params
func Setup(logger *utility.Logger, name string) *Configuration {
	var baseConfiguration *BaseConfig

	viper.SetConfigName(name)
	viper.SetConfigType("env")
	viper.AddConfigPath(".")
	viper.AutomaticEnv()

	if err := viper.ReadInConfig(); err != nil {
		log.Fatalf("Error reading config file, %s", err)
	}

	err := viper.Unmarshal(&baseConfiguration)
	if err != nil {
		log.Fatalf("Unable to decode into struct, %v", err)
	}

	configuration := baseConfiguration.SetupConfigurationn()

	// Params = configuration.Params
	Config = configuration
	logger.Info("configurations loading successfully")
	return configuration
}

// GetConfig helps you to get configuration data
func GetConfig() *Configuration {
	return Config
}
