package parser

type Parser interface {
	GetMessage()
	ParseMessage()
}

func Init() Parser {
	o := &p{}
	return o
}