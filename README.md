# openscrabble

## Tile Data
The entire board is made up of 225 tiles, each represented by 10 bits.
### Bits 1-3
6 possible tile modifiers (unchangeable)
No modifier, Triple word, Triple letter, Double word, Double letter, Spare
### Bits 4-8
32 possible tile values (modifiable letter value)
All 26 letters, blank tile, and 7 spare slots
### Bits 9-10
User (who placed the tile)
Up to 4 users

## Playing
- The user who's turn it is clicks a tile in their hand (JS onclick saves the letter to variable based on its content)
- Once they have a word they need to tot up their score
- The user adds the score onto the scoresheet
- They then press GO to allow the next player to go
- The current board's data is posted to the server
- When another player refreshes the page, all the tile data and scoresheet is retrieved
