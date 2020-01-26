define('renderer', ['map'], function (Map) {
    var area = function (region) {
        var minX = Number.MAX_VALUE, maxX = 0, minY = Number.MAX_VALUE, maxY = 0;

        _.each(region, function(pt) {
            if (pt[0] < minX) minX = pt[0];
            if (pt[0] > maxX) maxX = pt[0];
            if (pt[1] < minY) minY = pt[1];
            if (pt[1] > maxY) maxY = pt[1];
        });

        return (maxX - minX) * (maxY - minY);
    }

    var inRegion = function (region, pt) {
        var pt = {
            x: pt[0],
            y: pt[1]
        };

        var poly = region;

        for (var c = false, i = -1, l = poly.length, j = l - 1; ++i < l; j = i)
            ((poly[i][1] <= pt.y && pt.y < poly[j][1]) || (poly[j][1] <= pt.y && pt.y < poly[i][1]))
            && (pt.x < (poly[j][0] - poly[i][0]) * (pt.y - poly[i][1]) / (poly[j][1] - poly[i][1]) + poly[i][0])
            && (c = !c);
        return c;
    }

    var fillRegion = function(region, count) {
        var minX = 1e10;
        var minY = 1e10;
        var maxX = 0;
        var maxY = 0;

        _.each(region, function(pt) {
            if (pt[0] > maxX) maxX = pt[0];
            if (pt[0] < minX) minX = pt[0];
            if (pt[1] > maxY) maxY = pt[1];
            if (pt[1] < minY) minY = pt[1];
        });

        var tmp = [];

        while (count) {
            var rndX = Math.random() * (maxX - minX) + minX;
            var rndY = Math.random() * (maxY - minY) + minY;

            if (inRegion(region, [rndX, rndY])) {
                --count;
                tmp.push([rndX, rndY]);
            }
        }

        return tmp;
    };

    var getZoneCenter = function(zone) {
        var x = 0, y = 0, n = 0;

        _.each(zone, function(region) {
            _.each(region, function(tri) {
                x += tri.v0.x + tri.v1.x + tri.v2.x;
                y += tri.v0.y + tri.v1.y + tri.v2.y;
                n += 3;
            });
        });

        return [x / n, y / n];
    };

    var getZoneSize = function(zone) {
        var minX = 10e10, minY = 10e10, maxX = 0, maxY = 0;

        _.each(zone, function(region) {
            _.each(region, function(tri) {
                if (tri.v0.x < minX) {
                    minX = tri.v0.x;
                }

                if (tri.v0.y < minY) {
                    minY = tri.v0.y;
                }

                if (tri.v1.x < minX) {
                    minX = tri.v1.x;
                }

                if (tri.v1.y < minY) {
                    minY = tri.v1.y;
                }

                if (tri.v2.x < minX) {
                    minX = tri.v2.x;
                }

                if (tri.v2.y < minY) {
                    minY = tri.v2.y;
                }


                if (tri.v0.x > maxX) {
                    maxX = tri.v0.x;
                }

                if (tri.v0.y > maxY) {
                    maxY = tri.v0.y;
                }

                if (tri.v1.x > maxX) {
                    maxX = tri.v1.x;
                }

                if (tri.v1.y > maxY) {
                    maxY = tri.v1.y;
                }

                if (tri.v2.x > maxX) {
                    maxX = tri.v2.x;
                }

                if (tri.v2.y > maxY) {
                    maxY = tri.v2.y;
                }
            });
        });

        return [minX, minY, maxX, maxY];
    }

    var mapArray;
    var innerArray;
    var trianglesArray;
    var zoom = 0;
    var zoomDirection = 0;

    return {
        init: function() {
            mapArray = Map;

            innerArray = [];
            _.each(mapArray, function(zone) {
                var innerZone = [];

                _.each(zone, function (region) {
                    innerZone.push(fillRegion(region, Math.ceil(area(region) / 2000)));
                });

                innerArray.push(innerZone);
            });

            var sumArray = [];
            for (var j = 0; j < mapArray.length; ++j) {
                var zone = [];

                for (var k = 0; k < mapArray[j].length; ++k) {
                    zone.push(mapArray[j][k].concat(innerArray[j][k]));
                }

                sumArray.push(zone);
            }

            trianglesArray = [];

            _.each(sumArray, function (zone) {
                var trianglesZone = [];

                _.each(zone, function(region) {
                    var triangles = [];
                    var indeces = Delaunay.triangulate(region);
                    var a, b, c;

                    for (var i = indeces.length; i;) {
                        --i; a = region[indeces[i]];
                        --i; b = region[indeces[i]];
                        --i; c = region[indeces[i]];

                        if (true /* inRegion(region, [(a[0] + b[0] + c[0]) / 3, (a[1] + b[1] + c[1]) / 3]) */) {
                            var triangle = {
                                v0: {
                                    x: a[0],
                                    y: a[1]
                                },
                                v1: {
                                    x: b[0],
                                    y: b[1]
                                },
                                v2: {
                                    x: c[0],
                                    y: c[1]
                                }
                            };

                            triangle.alpha = Math.random() * 0.4;
                            triangle.direction = Math.random() * 10 > 5 ? 1 : -1;

                            triangles.push(triangle);
                        }
                    }

                    trianglesZone.push(triangles);
                });

                trianglesArray.push(trianglesZone);
            });
        },

        animate: function () {
            if (zoomDirection) {
                zoom += zoomDirection;

                if (zoom >= 100) {
                    zoom = 100;
                }

                if (zoom <= 0) {
                    zoom = 0;
                    zoomDirection = 0;
                }

                var zoneSize = getZoneSize(trianglesArray[window.clickedZone]);
                var sizeX = zoneSize[2] - zoneSize[0];
                var sizeY = zoneSize[3] - zoneSize[1];

                window.scaleX = 1 + (1024 / sizeX - 1) * zoom / 100;
                window.scaleY = 1 + (768 / sizeY - 1) * zoom / 100;
                window.scaleX = window.scaleY = window.scaleX > window.scaleY ? window.scaleY : window.scaleX;

                var zoneCenter = getZoneCenter(trianglesArray[window.clickedZone]);

                window.translateX = -(zoneCenter[0] * window.scaleX - 1024 / 2) * zoom / 100;
                window.translateY = -(zoneCenter[1] * window.scaleY - 768 / 2) * zoom / 100;
            }

            _.each(trianglesArray, function(zone) {
                _.each(zone, function(region) {
                    _.each(region, function (tri) {
                        tri.alpha += 0.005 * tri.direction;

                        if (tri.alpha > 0.4) {
                            tri.direction = -1;
                        }

                        if (tri.alpha < 0.1) {
                            tri.direction = 1;
                        }
                    });
                });
            });
        },

        click: function(x, y) {
            var clickedZoneFound = false;
            for (var i = 0; i < mapArray.length; ++i) {
                var zone = mapArray[i];

                for (var j = 0; j < zone.length; ++j) {
                    var region = zone[j];

                    if (x && y) {
                        if (inRegion(region, [x, y])) {

                            if (zoomDirection == 0 && zoom == 0) {
                                window.clickedZone = i;
                            }

                            $('body').css('cursor', 'pointer');
                            clickedZoneFound = true;
                            break;
                        } else {
                            if (zoomDirection == 0 && zoom == 0) {
                                window.clickedZone = -1;
                            } else if (zoomDirection == 1 && zoom == 100) {
                                zoomDirection = -1;
                            }

                            $('body').css('cursor', 'default');
                        }
                    }
                }

                if (clickedZoneFound) {
                    if (zoomDirection == 0 && zoom == 0) {
                       zoomDirection = 1;
                    } else if (zoomDirection == 1 && zoom == 100) {
                        zoomDirection = -1;
                    }

                    break;
                }
            }
        },

        draw: function (el, x, y) {
            if (x) window.oldX = x;
            if (y) window.oldY = y;

            var selectedZoneFound = false;
            for (var i = 0; i < mapArray.length; ++i) {
                var zone = mapArray[i];

                for (var j = 0; j < zone.length; ++j) {
                    var region = zone[j];

                    if (x && y) {
                        if (inRegion(region, [x, y])) {
                            window.selectedZone = i;
                            $('body').css('cursor', 'pointer');
                            selectedZoneFound = true;
                            break;
                        } else {
                            window.selectedZone = -1;
                            $('body').css('cursor', 'default');
                        }
                    }
                }

                if (selectedZoneFound) {
                    break;
                }
            }

            var ctx = el.getContext('2d');

            ctx.setTransform(1, 0, 0, 1, 0, 0);
            if (window.translateX && window.translateY || window.scaleX && window.scaleY) {
                ctx.translate(window.translateX, window.translateY);
                ctx.scale(window.scaleX, window.scaleY);
            }

            ctx.fillStyle = "lightblue";
            ctx.fillRect(0, 0, el.width, el.height);

            var regionN = -1;
            for (var i = 0; i < mapArray.length; ++i) {
                var zone = mapArray[i];

                for (var j = 0; j < zone.length; ++j) {
                    //if ('clickedZone' in window && window.clickedZone != -1 && window.clickedZone != i) {
                    //    ctx.globalAlpha = (100 - zoom) / 100;
                    //} else {
                        ctx.globalAlpha = 1;
                    //}

                    ctx.save();
                    var region = zone[j];

                    ctx.fillStyle = [
                        '#6C3595',
                        '#F9BB0C', '#F9BB0C',
                        '#267BBF', '#267BBF',
                        '#E6414C',
                        '#F16133', '#F16133', '#F16133', '#F16133', '#F16133',
                        '#91C846',
                        '#DB522D', '#DB522D', '#DB522D',
                        '#2FADE2', '#2FADE2', '#2FADE2', '#2FADE2',
                        '#19AFA6', '#19AFA6',
                        '#EF3C86',
                        '#DC5186',
                        '#F59C31',
                        '#E63834', '#E63834', '#E63834'
                    ][++regionN];
                    ctx.strokeStyle = ctx.fillStyle;
                    ctx.beginPath();

                    if (region.length) {
                        ctx.moveTo(region[0][0], region[0][1]);

                        _.each(region, function(point) {
                            ctx.lineTo(point[0], point[1]);
                        });
                    }

                    ctx.closePath();
                    ctx.fill();
                    ctx.stroke();
                    ctx.clip();

                    ctx.fillStyle = 'white';
                    region = trianglesArray[i][j];

                    _.each(region, function(tri) {
                        ctx.beginPath();
                        ctx.moveTo(tri.v0.x, tri.v0.y);
                        ctx.lineTo(tri.v1.x, tri.v1.y);
                        ctx.lineTo(tri.v2.x, tri.v2.y);
                        ctx.closePath();

                        var f = 1;

                        //if ('clickedZone' in window && window.clickedZone != -1 && window.clickedZone != i) {
                        //    f = (100 - zoom) / 100;
                        //}

                        if (window.selectedZone === i) {
                            if (inRegion([[tri.v0.x, tri.v0.y], [tri.v1.x, tri.v1.y], [tri.v2.x, tri.v2.y]], [window.oldX, window.oldY])) {
                                ctx.globalAlpha = 0.85 * f;
                            } else {
                                ctx.globalAlpha = (tri.alpha + 0.2) * f;
                            }
                        } else {
                            if (!tri.alpha) {
                                ctx.globalAlpha = 0;
                            } else {
                                ctx.globalAlpha = tri.alpha * f;
                            }
                        }

                        ctx.fill();
                    });

                    ctx.restore();
                }
            }
        }
    }
});