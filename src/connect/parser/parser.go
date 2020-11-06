package parser

type Parser interface {
	GetMessage()
	ParseMessage()
}

func Init() (Parser, error) {
	return nil, nil
}