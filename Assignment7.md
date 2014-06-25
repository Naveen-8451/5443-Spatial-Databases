#### Assignment 7

select state,earth_quakes.shape from state_borders,earth_quakes where contains(state_borders.shape,earth_quakes.shape) and state="California"
