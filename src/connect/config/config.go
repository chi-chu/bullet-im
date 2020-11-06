package config

import (
	"encoding/json"
	"io/ioutil"
	"os"
)

type Config struct {
	LogLevel		int				`json:"log_level"`
	LogType			string			`json:"log_type"`

}

const configFilePath  	=  "./config.conf"

func InitConfig() *Config {
	f, err := os.OpenFile(configFilePath, os.O_RDONLY, 0777)
	if err != nil {
		log.Warnf("can`t find config.conf use default config")
		return defaultConfig()
	}
	data, err := ioutil.ReadAll(f)
	c := new(Config)
	err = json.Unmarshal(data, &c)
	if err != nil {
		log.Warnf("can`t find config.conf use default config")
		return defaultConfig()
	}
	return c
}

func defaultConfig() *Config {
	return &Config{
		1,
		"",
	}
}