use std::env;

#[derive(Debug)]
pub struct Config {
	pub port: i32,
}

pub fn get args() -> Config {
	let pattern = env::args().nth(1).expect("no pattern given");
	println!();
}