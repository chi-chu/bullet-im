package parser

import "github.com/chi-chu/bullet-im/src/connect/message"

type Parser interface {
	EncodeMessage(*message.Message) []byte
	DecodeMessage([]byte) *message.Message
}

func Init() Parser {
	o := &p{}
	return o
}