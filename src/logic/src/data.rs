use std::collections::HashMap;
use std::sync::RwLock;
use chrono::Local;
#[macro_use]
extern crate lazy_static;


struct Global {
    data: HashMap<String, String>
}

lazy_static!{
    static ref CACHE: RwLock<Global> = {
        RwLock::new(Global{
            data: HashMap::new()
        })
    };
}