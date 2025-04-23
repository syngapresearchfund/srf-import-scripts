#!/bin/bash

postIds=( 2411 2414 2413 2409 2410 2412 2393 2408 2406 2389 2390 2391 2392 2394 2395 2396 2397 2398 2399 2400 2401 2402 2403 2404 2405 2407 )

for p in ${postIds[@]}; do
  wp post term add $p srf-events-category Fundraisers
done