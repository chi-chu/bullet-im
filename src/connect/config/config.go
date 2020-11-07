package config

import (
	"encoding/json"
	"github.com/chi-chu/log"
	"io/ioutil"
	"os"
)

type Config struct {
	LogLevel		int				`json:"log_level"`
	LogType			string			`json:"log_type"`
	Port			int				`json:"port"`
	MsgCacheSize	uint			`json:"msg_cache_size"`
	CertFile		string			`json:"cert_file"`
	KeyFile			string			`json:"key_file"`
	HeartBeatTime	int				`json:"heart_beat_time"`
}

const configFilePath  	=  "./config.conf"

func InitConfig() *Config {
	f, err := os.OpenFile(configFilePath, os.O_RDONLY, 0777)
	if err != nil {
		log.Warn("can`t find config.conf use default config")
		return defaultConfig()
	}
	data, err := ioutil.ReadAll(f)
	c := new(Config)
	err = json.Unmarshal(data, &c)
	if err != nil {
		log.Warn("can`t find config.conf use default config")
		return defaultConfig()
	}
	return c
}

func defaultConfig() *Config {
	return &Config{
		1,
		"",
		8090,
		8096,
		"",
		"",
		60,
	}
}