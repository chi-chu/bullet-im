package storage

type Storage interface {
	GetConn()
	SetConn()
}

func Init() (Storage, error) {
	return nil, nil
}