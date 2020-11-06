package server

import (
	"github.com/chi-chu/bullet-im/src/connect/config"
	"github.com/chi-chu/bullet-im/src/connect/parser"
	"github.com/chi-chu/bullet-im/src/connect/rpc"
	"github.com/chi-chu/bullet-im/src/connect/storage"
)

func Start(storage storage.Storage, parser parser.Parser, rpc rpc.Server, config *config.Config) error {

	return nil
}