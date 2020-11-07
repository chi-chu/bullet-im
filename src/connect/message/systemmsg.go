package message

type systemMsg struct {

}

func newSystemMsg() *systemMsg {
	return &systemMsg{}
}

func pingMsg() []byte{
	return nil
}