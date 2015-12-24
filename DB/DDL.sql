CREATE DATABASE `itunes`;

-- =================================================
-- Definition of album table
CREATE TABLE album (
  id INT(11) NOT NULL PRIMARY KEY auto_increment,
  title VARCHAR(255) NOT NULL,
  release_year INT NOT NULL,
  price INT NOT NULL,
  description TEXT NOT NULL
);

-- =================================================
-- Definition of song table
CREATE TABLE `song` (
  id INT(11) NOT NULL PRIMARY KEY auto_increment,
  title VARCHAR(255) NOT NULL,
  album_id INT(11) NOT NULL,
  price INT NOT NULL,
  duration INT NOT NULL,
  description TEXT NOT NULL,
  FOREIGN KEY (album_id)
      REFERENCES album(id)
      ON UPDATE CASCADE ON DELETE CASCADE
);

-- =================================================
-- Definition of artist table
CREATE TABLE `artist` (
  id INT(11) NOT NULL PRIMARY KEY auto_increment,
  name VARCHAR(255) NOT NULL,
  family VARCHAR(255) NOT NULL,
  description TEXT NOT NULL
);

-- =================================================
-- Definition of song_artist table
CREATE TABLE `song_artist` (
  song_id INT(11) NOT NULL,
  artist_id INT(11) NOT NULL,
  role VARCHAR(255) NOT NULL,
  PRIMARY KEY (song_id, artist_id),
  FOREIGN KEY (song_id)
  REFERENCES song(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (artist_id)
  REFERENCES artist(id)
    ON UPDATE CASCADE ON DELETE CASCADE
);

-- =================================================
-- Definition of song_genre table
CREATE TABLE `song_genre` (
  song_id INT(11) NOT NULL,
  genre VARCHAR(255) NOT NULL,
  PRIMARY KEY (song_id, genre),
  FOREIGN KEY (song_id)
  REFERENCES song(id)
    ON UPDATE CASCADE ON DELETE CASCADE
);

-- =================================================
-- Definition of song_artist table
CREATE TABLE `artist_genre` (
  artist_id INT(11) NOT NULL,
  genre VARCHAR(255) NOT NULL,
  PRIMARY KEY (artist_id, genre),
  FOREIGN KEY (artist_id)
  REFERENCES artist(id)
    ON UPDATE CASCADE ON DELETE CASCADE
);

-- =================================================
-- Definition of user table
CREATE TABLE `user` (
  id INT(11) NOT NULL PRIMARY KEY auto_increment,
  name VARCHAR(255) NOT NULL,
  family VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  username VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL
);

-- =================================================
-- Definition of playlist table
CREATE TABLE `playlist` (
  user_id INT(11) NOT NULL,
  name VARCHAR(255) NOT NULL,
  PRIMARY KEY (user_id, name),
  FOREIGN KEY (user_id)
  REFERENCES user(id)
    ON UPDATE CASCADE ON DELETE CASCADE
);

-- =================================================
-- Definition of playlist_song table
CREATE TABLE `playlist_song` (
  song_id INT(11) NOT NULL,
  user_id INT(11) NOT NULL,
  name VARCHAR(255) NOT NULL,
  PRIMARY KEY (user_id, song_id, name),
  FOREIGN KEY (user_id)
  REFERENCES user(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (song_id)
  REFERENCES song(id)
    ON UPDATE CASCADE ON DELETE CASCADE
);

-- =================================================
-- Definition of review table
CREATE TABLE `review` (
  song_id INT(11) NOT NULL,
  user_id INT(11) NOT NULL,
  review_text TEXT,
  star INT(1) NOT NULL,
  PRIMARY KEY (user_id, song_id),
  FOREIGN KEY (user_id)
  REFERENCES user(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (song_id)
  REFERENCES song(id)
    ON UPDATE CASCADE ON DELETE CASCADE
);

-- =================================================
-- Definition of transaction table
CREATE TABLE `transaction` (
  user_id INT(11) NOT NULL,
  payment_number INT(11) NOT NULL,
  PRIMARY KEY (user_id, payment_number),
  FOREIGN KEY (user_id)
  REFERENCES user(id)
    ON UPDATE CASCADE ON DELETE CASCADE
);


  SELECT * FROM artist WHERE id NOT IN (
    SELECT SA1.artist_id FROM song_artist AS SA1,song_artist AS SA2,artist_genre AS A
    WHERE SA1.song_id = SA2.song_id AND SA1.artist_id != SA2.artist_id AND SA2.artist_id = A.artist_id AND A.genre='rap' );


-- =================================================
-- Definition of order table
CREATE TABLE `order` (
  song_id INT(11) NOT NULL,
  user_id INT(11) NOT NULL,
  payment_number INT NOT NULL,
  PRIMARY KEY (user_id, payment_number, song_id),
  FOREIGN KEY (user_id, payment_number)
  REFERENCES transaction(user_id, payment_number)
    ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (song_id)
  REFERENCES song(id)
    ON UPDATE CASCADE ON DELETE CASCADE
);

-- 1) Showing all pop music in order of price (correct)

SELECT * FROM song  WHERE id NOT IN (SELECT song_id FROM song_genre WHERE genre='POP') ORDER BY price;

-- 2) Correct
SELECT DISTINCT song.id, song.title, SUM(review.star) FROM song INNER JOIN review ON song.id = review.song_id WHERE song_id IN (SELECT song_id from song_genre where genre='rock')
GROUP BY song.id ORDER BY SUM(review.star) DESC;

-- 3) Making an order for 3 music with price 2000,3000,4000 (correct)
start transaction;
INSERT INTO `transaction` (user_id, payment_number) VALUES (1,1);
INSERT INTO `order` (user_id, payment_number, song_id) VALUES (1,1,2);
INSERT INTO `order` (user_id, payment_number, song_id) VALUES (1,1,9);
INSERT INTO `order` (user_id, payment_number, song_id) VALUES (1,1,10);
commit;

-- 4) correct

SELECT * FROM user WHERE id IN (SELECT user_id FROM `order` GROUP BY user_id, payment_number HAVING COUNT(song_id)>1);

-- 5) Correct

SELECT DISTINCT artist.id,artist.name,artist.family, COUNT(genre) FROM artist INNER JOIN artist_genre ON artist.id=artist_genre.artist_id GROUP BY artist_id ORDER BY COUNT(genre);

-- 6)
SELECT DISTINCT artist.* FROM artist,song_artist,song AS s1,song AS s2 WHERE artist.id = song_artist.artist_id
                AND song_artist.song_id = s1.id AND s1.album_id=s2.album_id AND s1.id != s2.id;

  -- 7) Correct


SELECT * FROM artist AS A1 WHERE EXISTS (
  SELECT SA1.song_id FROM song_artist AS SA1, song_artist AS SA2, artist AS A2
         WHERE SA1.song_id = SA2.song_id AND SA1.artist_id = A1.id AND SA2.artist_id = A2.id
              AND A2.name = 'Hich' AND A1.name != 'Hich'
);

-- 8) Correct

SELECT * FROM artist WHERE id NOT IN (
  SELECT SA1.artist_id FROM song_artist AS SA1,song_artist AS SA2,artist_genre AS A
  WHERE SA1.song_id = SA2.song_id AND SA1.artist_id != SA2.artist_id AND SA2.artist_id = A.artist_id AND A.genre='rap' );

-- 9)
SELECT artist_id FROM song_artist  INNER JOIN song ON song_artist.song_id = song.id GROUP BY album_id ORDER BY COUNT(DISTINCT album_id);

-- 10)
select  f.username
from
(select user_id,COUNT(song_id) as v1
from  `order`
group by user_id) as m ,
(select y.username, y.id , count(distinct review.song_id) as v2
from user as y INNER join `order` as z ON y.id=z.user_id INNER join review ON y.id=review.user_id
group by y.username) as f
where f.id = m.user_id and
2*f.v2 > m.v1