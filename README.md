# Development Environment Setup

* Development Setup is dockerized
* Container managed with in repo itself
* Docker container is managing 3 container
  * Innoscripta Web - innoscriptanginx
  * Innoscripta App - innoscriptaapp
  * Innoscripta DB - innoscriptamysql
  * Innoscripta Cache - innoscripta-redis

## Build and Run Containers

### Follow the commands to build and run the containers
  * Download and install Docker (https://docs.docker.com/engine/install/)
  * Clone the repo (https://github.com/vinjithkannan/innoscripta-test-news-backend.git) 
  
  * ```shell
    git clone https://github.com/vinjithkannan/innoscripta-test-news-backend.git
    git fetch origin docker-development  
    git checkout docker-development
    
    cd <path of the directory cotais the source>   
    \> docker-compose up --build  # only for very first time    
       # once build completed terminal will show the three containers are running
       # from next time up and run only need
    \> docker-compose up
    
    ````
  * Once containers where up, dev env will able to browse with url
  #### (http://localhost)

  * ```shell
    docker exec -it innoscriptaapp sh
    /var/www/# composer install
    /var/www/# php artisan migrate
  ```  

### API Documentation
* Api Collection added from post man exported file Innoscripta News.postman_collection.json
