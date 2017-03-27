## What's this?

This is Goblin War! This is a RESTful engine to build simple but strongly scalable roguelike games.

## Install

Installation is a classical one! 

1. Clone project 

`git clone git@github.com:enrike1983/goblin_war.git`

2. Install vendors ( ensure to have [Composer](https://getcomposer.org/doc/00-intro.md) installed globally: )

`composer install`

3. Setup the database with your parameters.

4. Update the database with the schema

`php bin/console doctrine:schema:update --force`

5. Load fixtures ( if you wanna try the app with an existing user )

`php bin/console doctrine:fixture:load`


## Game Rules

The game rules are very easy and very common for a roguelike:

1. The World is a "chessboard" m x n ( configurable )
2. Once the player is born he is free to explore the world using 4 simple commands ( forward, left, right, back )
3. During the exploration the player could meet monsters with a certain probability.
4. If the player begin fighting with a monster the battle starts and the user is not able to move until the fight has finished
5. The Battle is a simple comparison of the attack skill of the player and the monster. The higher wins! :)
6. If the player wins gains the experience "shipped" by the monster and is added to the current experience.
7. If the player loses does not gain any experience and takes 5 points of damage
8. If the energy of the player reaches 0 he dies and is not able to move any more! You need to create a new character

### Game notes

* Player attack value is auto generated when the player is created.
* Monster's attack value, experience and name are auto generated at runtime when the monster spawns.
* The world is a YAML file m x n where every "room" has its own properties ( to be implemented in future )
`goblin_war/src/AppBundle/Resources/config/dungeon.yml`

## Developer's Guide

This is a RESTful engine, there is not any working client developed at the moment.

Every interaction is supposed to be done using something like postman or another rest client.

To interact with the app you need to have a valid user and its X-AUTH-TOKEN for every request, authentication is stateless.

#### Header Call Example

`X-AUTH-TOKEN: ce7b96be7cf9dda41450b99af5a3eb3885aa8a1af442`

### Player Creation

#### API `[POST] /api/player/create`

Creates a new player
 
#### Return Example

```json
{
 "player_profile": {
     "name": "player@player.com",
     "attack": 41,
     "life": 0,
     "api_id": "123123123123"
 }
}
```

### API Movement documentation

#### API `[GET] /api/movement/current-position`

Get the current position in the World.
 
#### Return Example
```json
{
 "player_profile": {
     "name": "player@player.com",
     "attack": 41,
     "life": 0
 },
 "navigation": {
     "forward": false,
     "left": {
         "name": "Room 1",
         "id": 2
     },
     "right": false,
     "back": {
         "name": "Room 2",
         "id": 6
     },
     "current": {
         "name": "Room 3",
         "id": 3
     }
 }
}
```

#### API `[GET] /api/movement/forward`

Make the player move forward and return the new position in the World 

#### Return Example 

Similar to _/api/movement/current-position_

#### API `[GET] /api/movement/back`

Make the player move back and return the new position in the World 

#### Return Example 

Similar to _/api/movement/current-position_

#### API `[GET] /api/movement/right`

Make the player move right and return the new position in the World 

#### Return Example 

Similar to _/api/movement/current-position_

#### API `[GET] /api/movement/left`

Make the player move left and return the new position in the World 

#### Return Example 

Similar to _/api/movement/current-position_
 
### Battle

#### API `[GET] /api/battle/fight`

Fight with the current monster

#### Return Example 

```json
{
    "fight_result": 3,
    "fight_message": "loser"
}
```

