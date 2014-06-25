## Assignment 7

#### sql query for earth_quakes in california state

```sql

select state,earth_quakes.shape from state_borders,earth_quakes where contains(state_borders.shape,earth_quakes.shape) and state="California"
```
#### sql query for volcanoes in california state

```sql

select state,volcanoes.shape from state_borders,volcanoes where contains(state_borders.shape,volcanoes.shape) and state="California"
```
