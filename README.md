# Laravel Docker-Compose
##### Run docker-compose command from base path of Laravel project

The docker-compose files should be in directory: ./docker

Example:

Run `docker-compose up -d`

    ldc run
    
It's like

    cd ./docker && docker-compose up -d
    
Run `docker-compose kill`    
    
    ldc kill
    
It's like

    cd ./docker && docker-compose kill


And run `docker-compose {some command}`

    ldc cmd {some command}

Restart `docker-compose kill {container?} && docker-compose up -d  {container?}`

    ldc restart {container?}
    
`container` - optional argument, container name. If container empty then will be restart all containers.
