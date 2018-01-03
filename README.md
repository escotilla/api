# escotilla api

### Build
`docker-compose build`

### Start
`docker-compose up`

##### First time setup
Get mongo container ID
`docker ps`

Connect to container `docker exec -it CID /bin/bash`

Add admin user `mongo admin --eval "db.createUser({user: 'escotilla', pwd: 'test', roles: [{ role: 'dbOwner', db: 'escotilla' }]})"`
