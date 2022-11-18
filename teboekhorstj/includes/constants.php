<?php
/**
 * This is for my WEBD-3201 course
 * This file contains constants to be used throughout the whole website
 *
 * PHP Version 7.2
 *
 * @author Jaxon teBoekhorst
 * @version 1.0(September, 13, 2022)
 */

/***** COOKIES *****/
/***/
const COOKIE_LIFESPAN = "2592000";

/***** USER TYPES *****/
/***/
const ADMIN = "s";
const AGENT = "a";
const CLIENT = "c";
const PENDING = "p";
const DISABLED = "d";

/***** DATABASE CONSTANTS *****/
/***/
const DB_HOST = "127.0.0.1";
const DATABASE = "teboekhorstj_db";
const DB_ADMIN = "teboekhorstj";
const DB_PORT = "5432";
const DB_PASSWORD = "100821229";

/***** OTHER CONSTANTS *****/
/** This is for the amount of results per table page */
const RESULTS_PER_PAGE = 10;
const IMAGE_HEADERS = ['logoPath'];

/***** FILE UPLOAD CONSTANTS *****/
/***/
const MAX_FILE_SIZE = 3000000;
const MAX_SIZE_STR = '3MB';
const ACCEPTED_FILE_TYPES = ['jpg', 'jpeg', 'jpe', 'png', 'gif', 'svg'];