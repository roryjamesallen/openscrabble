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
