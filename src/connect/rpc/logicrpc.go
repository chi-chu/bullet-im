package rpc

import (
	"github.com/chi-chu/bullet-im/src/connect/config"
)

type Server interface {
	HandleMsg()
	GetSyncMsg()
}

type logicServer struct {

}

func InitClient(c *config.Config) (Server, error) {

	return nil, nil
}