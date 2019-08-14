<html>
    <head>

        <link rel="stylesheet" href="css/reset.css" type="text/css" />
        <script>
            var keysDown = [];
            var keys =
            {
                w:87,
                a:65,
                s:83,
                d:68
            };

            var level =
            {
                width:window.innerWidth * 0.9999999,
                height:window.innerHeight * 0.9999999,
                bgColor:"#aaaadd"
            };

            var groundHeight = 25;
            var grassHeight = 125;

            var totalGroundHeight = groundHeight + grassHeight;

            var platforms =
            [
                {
                    x:0,
                    y:level.height - groundHeight,
                    width:level.width,
                    height:groundHeight,
                    bgColor:"#993300",
                    isSolid:true,
                    opacity:1
                },
                {
                    x:0,
                    y:level.height - groundHeight - grassHeight,
                    width:level.width,
                    height:grassHeight,
                    bgColor:"#993300",
                    isSolid:true,
                    opacity:0.5
                }
            ];

            var adtGravity = 0.25;

            var maxVelocity =
            {
                x:25,
                y:25
            };
            var maxAcceleration =
            {
                x:5,
                y:5
            };
            var player =
            {
                x:0,
                y:0,
                width:50,
                height:100,
                bgColor:"#000000",
                bgImage:null,
                opacity:1,
                solid:true,
                velocity:
                {
                    x:0,
                    y:0
                },
                acceleration:
                {
                    x:0,
                    y:adtGravity
                },
                jerk:
                {
                    x:1,
                    y:1
                }
            };

            var canvas = null;
            var ctx = null;
            var canvasID = "canvas";
            function Main()
            {
                document.body.addEventListener("keydown", function(e)
                {
                    handleKeydown(e);
                });

                document.body.addEventListener("keyup", function(e)
                {
                    handleKeyup(e);
                });

                canvas = document.getElementById(canvasID);
                if(canvas)
                {
                    canvas.width = level.width;
                    canvas.height = level.height;

                    ctx = canvas.getContext("2d");
                }

                if(ctx)
                {
                    ctx.fillStyle = "#FF00DD";
                    ctx.fillRect(10, 10, 10, 10);
                }

                initStairs();
                var options = null;
                initIsland(options);

                animate();
            }

            function animate()
            {
                ctx.fillStyle = level.bgColor;
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                for(var i = 0; i < platforms.length; i++)
                {
                    ctx.globalAlpha = platforms[i].opacity;
                    ctx.fillStyle = platforms[i].bgColor;
                    ctx.fillRect(platforms[i].x, platforms[i].y, platforms[i].width, platforms[i].height);
                }

                ctx.globalAlpha = player.opacity;
                ctx.fillStyle = player.bgColor;
                ctx.fillRect(player.x, player.y, player.width, player.height);

                console.log(keysDown.toString())
                for(var i = 0; i < keysDown.length; i++)
                {
                    switch(keysDown[i])
                    {
                        case keys.w:
                            if(player.acceleration.y + (-1) * player.jerk.y <= maxAcceleration.y)
                            {
                                player.acceleration.y += (-1) * player.jerk.y;
                            }
                            break;
                        case keys.a:
                            if(player.acceleration.x + (-1) * player.jerk.x <= maxAcceleration.x)
                            {
                                player.acceleration.x += (-1) * player.jerk.x;
                            }
                            break;
                        case keys.s:
                            if(player.acceleration.y + player.jerk.y <= maxAcceleration.y)
                            {
                                player.acceleration.y += player.jerk.y;
                            }
                            break;
                        case keys.d:
                            if(player.acceleration.x + player.jerk.x <= maxAcceleration.x)
                            {
                                player.acceleration.x += player.jerk.x;
                            }
                            break;
                    }
                }

                var cm = canMove(player);
                if(cm.x)
                {
                    player.x += player.velocity.x;
                    if(player.x < 0)
                    {
                        player.x = 0;
                    }
                    else if(player.x + player.width > level.width)
                    {
                        player.x = level.width - player.width;
                    }
                  }

                  if(cm.y)
                  {
                    player.y += player.velocity.y;
                    if(player.y < 0)
                    {
                        player.y = 0;
                    }
                    else if(player.y + player.height > level.height)
                    {
                        player.y = level.height - player.height;
                    }

                    if(player.velocity.x >= ((-1) * maxVelocity.x) && player.velocity.x <= maxVelocity.x)
                    {
                        player.velocity.x += player.acceleration.x;
                    }
                    if(player.velocity.y >= ((-1) * maxVelocity.y) && player.velocity.y <= maxVelocity.y)
                    {
                        player.velocity.y += player.acceleration.y + adtGravity;
                    }
                }

                requestAnimationFrame(animate);
            }

            function canMove(object)
            {

                // Check collisions
                var canMove = {x:true, y:true};
                var objectBox = getBBox(object);

                for(var i = 0; i < platforms.length; i++)
                {

                    if(!(platforms[i].isSolid))
                      continue;

                    var platformBox = getBBox(platforms[i]);

                    if(object.acceleration.y > 0)
                    {
                        //console.log("collision check down")
                        //if(objectBox.right > platformBox.left && objectBox.left < platformBox.right && objectBox.bottom + object.velocity.y > platformBox.top)
                        //console.log("Pt:" + objectBox.top + " > pb:" + platformBox.bottom + ", Pr:" + objectBox.right + " >= pl:" + platformBox.left + " && Pr:" + objectBox.right + " <= pr:" + platformBox.right + " || Pl:" + objectBox.left + " <= pr:" + platformBox.right + " && Pl:" + objectBox.left + " >= pl:" + platformBox.left);

                        if((objectBox.bottom + object.velocity.y >= platformBox.top && objectBox.bottom < platformBox.bottom)
                            && ((objectBox.right >= platformBox.left && objectBox.right <= platformBox.right) || (objectBox.left <= platformBox.right && objectBox.left >= platformBox.left)))
                        {
                          console.log("DOWNGOING~~Pt:" + objectBox.top + " > pb:" + platformBox.bottom + ", Pr:" + objectBox.right + " >= pl:" + platformBox.left + " && Pr:" + objectBox.right + " <= pr:" + platformBox.right + " || Pl:" + objectBox.left + " <= pr:" + platformBox.right + " && Pl:" + objectBox.left + " >= pl:" + platformBox.left);
                            object.velocity.y *= (1 - platforms[i].opacity);

                            if(object.velocity.y == 0 && platforms[i].opacity == 1)
                            {
                                object.y = platformBox.top - object.height;
                                canMove.y = false;
                            }
                        }
                    }
                    else if(object.acceleration.y < 0 && platforms[i].opacity == 1)
                    {
                        if((objectBox.top + object.velocity.y >= platformBox.bottom && objectBox.top < platformBox.top)
                            && ((objectBox.right >= platformBox.left && objectBox.right <= platformBox.right) || (objectBox.left <= platformBox.right && objectBox.left >= platformBox.left)))
                        {
                          console.log("UPGOING~~Pt:" + objectBox.top + " > pb:" + platformBox.bottom + ", Pr:" + objectBox.right + " >= pl:" + platformBox.left + " && Pr:" + objectBox.right + " <= pr:" + platformBox.right + " || Pl:" + objectBox.left + " <= pr:" + platformBox.right + " && Pl:" + objectBox.left + " >= pl:" + platformBox.left);
                            object.velocity.y *= (1 - platforms[i].opacity);

                            if(object.velocity.y == 0)
                            {
                                object.y = platformBox.bottom;
                                canMove.y = false;
                            }
                        }
                    }

                    if(object.acceleration.x < 0 && platforms[i].opacity == 1)
                    {
                      if((objectBox.left + object.velocity.x < platformBox.right && objectBox.left > platformBox.right)
                        && ((objectBox.top < platformBox.bottom) || (objectBox.bottom > platformBox.top)))
                        {
                          console.log("LEFTGOING~~Pt:" + objectBox.top + " > pb:" + platformBox.bottom + ", Pr:" + objectBox.right + " >= pl:" + platformBox.left + " && Pr:" + objectBox.right + " <= pr:" + platformBox.right + " || Pl:" + objectBox.left + " <= pr:" + platformBox.right + " && Pl:" + objectBox.left + " >= pl:" + platformBox.left);
                            object.velocity.x *= (1 - platforms[i].opacity);

                            if(object.velocity.x == 0)
                            {
                                object.x = platformBox.right;
                                canMove.x = false;
                            }
                        }
                    }
                    else if(object.acceleration.x > 0 && platforms[i].opacity == 1)
                    {
                      if((objectBox.right + object.velocity.x >= platformBox.left && objectBox.left < platformBox.left)
                        && ((objectBox.top < platformBox.bottom) || (objectBox.bottom > platformBox.top)))
                        {
                          console.log("RIGHTGOING~~Pt:" + objectBox.top + " > pb:" + platformBox.bottom + ", Pr:" + objectBox.right + " >= pl:" + platformBox.left + " && Pr:" + objectBox.right + " <= pr:" + platformBox.right + " || Pl:" + objectBox.left + " <= pr:" + platformBox.right + " && Pl:" + objectBox.left + " >= pl:" + platformBox.left);
                            object.velocity.x *= (1 - platforms[i].opacity);

                            if(object.velocity.x == 0)
                            {
                                object.x = platformBox.left;
                                canMove.x = false;
                            }
                        }
                    }

                }

                return canMove;
            }

            function initStairs()
            {

              var stairsInitialHeight = 400;
              var stairsHeightDifference = 45;

              var stairWidth = 75;
              var stairHeight = 0;

              var numStairs = 9;

              for(var i = 0; i < numStairs; i++)
              {
                stairHeight = stairsInitialHeight - (i * stairsHeightDifference);
                platforms.push(
                {
                    x:stairWidth * i,
                    y:level.height - stairHeight - totalGroundHeight,
                    width:stairWidth,
                    height:stairHeight,
                    bgColor:"#ff8888",
                    isSolid:true,
                    opacity:1
                });
              }
            }

            function initIsland(options)
            {
              if(options)
              {
                var islandWidth = 300;
                var islandHeight = 25;

              }
            }

            function handleKeydown(e)
            {
                var k = e.keyCode;
                var index = keysDown.indexOf(k);
                if(index == -1)
                {
                    //console.log("key: " + k)
                    /*
                    switch(k)
                    {
                        case keys.w:
                            player.acceleration.y = (-1) * maxAcceleration.y;
                            break;
                        case keys.s:
                            player.acceleration.y = maxAcceleration.y;
                            break;
                        case keys.a:
                            player.acceleration.x = (-1) * maxAcceleration.x;
                            break;
                        case keys.d:
                            player.acceleration.x = maxAcceleration.x;
                            break;
                    }
                    */
                    keysDown.push(k);
                }

            }

            function handleKeyup(e)
            {
                var k = e.keyCode;
                var index = keysDown.indexOf(k);
                if(index > -1)
                {
                    keysDown.splice(index, 1);

                    switch(k)
                    {
                        case keys.w:
                            player.acceleration.y = adtGravity;
                            player.velocity.y = 0;
                            break;
                        case keys.s:
                            player.acceleration.y = adtGravity;
                            player.velocity.y = 0;
                            break;
                        case keys.a:
                            player.acceleration.x = 0;
                            player.velocity.x = 0;
                            break;
                        case keys.d:
                            player.acceleration.x = 0;
                            player.velocity.x = 0;
                            break;
                    }
                }

            }

            function getBBox(o)
            {
                return {
                    top:o.y,
                    bottom:o.y + o.height,
                    left:o.x,
                    right:o.x + o.width
                };
            }
        </script>
    </head>

    <body onload="Main()">
        <canvas id="canvas">

        </canvas>
    </body>
</html>
