var json,
    w = document.getElementById("d3container").clientWidth / 1.05,
    h = window.innerHeight - 70,
    rectW = 120,
    rectH = 50;


d3.xhr("index.php?r=rbac/default/items").get(function(error, XMLHttpRequest) {

    json = JSON.parse(XMLHttpRequest.response);

    var zoomListener = d3.behavior.zoom().scaleExtent([0.3, 1]).on("zoom", zoom);

    var vis = d3.select("#d3container").append("svg:svg")
        .attr("width", w)
        .attr("height", h)
        .call(zoomListener)
        .on("dblclick.zoom", null);

    vis.append("svg:defs").selectAll("marker")
        .data(["marker"])
        .enter().append("svg:marker")
        .attr("id", String)
        .attr("viewBox", "0 -3 10 6")
        .attr("refX", 10)
        .attr("markerWidth", 6)
        .attr("markerHeight", 6)
        .attr("orient", "auto")
        .append("svg:path")
        .attr("d", "M10,-3L0,0L10,3");

    var mainGroup = vis.append('g');
    var linksGroup = mainGroup.append("svg:g").attr('id', 'linksGroup');
    var nodesGroup = mainGroup.append("svg:g").attr("id", "nodesGroup");

    function zoom() {
        mainGroup.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
    }

    var nodes = function() {

        var localNodes = JSON.parse(localStorage.getItem('nodes'));

        if (localNodes !== null) {
            localNodes.forEach(function(d, i) {
                if (json.nodes[d.index] && json.nodes[d.index].name === d.name) {
                    json.nodes[d.index].x = d.x;
                    json.nodes[d.index].y = d.y;
                    json.nodes[d.index].px = d.px;
                    json.nodes[d.index].py = d.py;
                    json.nodes[d.index].weight = d.weight;
                    json.nodes[d.index].fixed = true;
                }
                else {
                    json.nodes.push(d);
                }
            });
        }
        return json.nodes;
    };

    var links = function() {

        var localLinks = JSON.parse(localStorage.getItem('links'));

        if (localLinks !== null) {
            localLinks.forEach(function(d, i) {
                if (json.links[d.index] && json.links[d.index].source.index === d.source.index && json.links[d.index].target.index === d.target.index) {

                }
                else {
                    json.links.push({
                        "source": d.source.index,
                        "target": d.target.index
                    });
                }
            });
        }
        return json.links;
    };

    var force = window.self.force = d3.layout.force()
        .nodes(nodes())
        .links(links())
        .linkDistance(function(link) {
            if (link.target.type === 2) {
                return h / 2;
            }
            return h / 4;
        })
        // .linkStrength(2)
        // .gravity(1)
        .charge(-7000)
        // .friction(0)
        .size([w, h])
        .on("tick", tick)
        .start()
        .alpha(0.2);

    var setLinks = function(data) {

        var links = linksGroup.selectAll("path")
            .data(data, function(d) {
                return d.source.index + "-" + d.target.index;
            });

        links.enter()
            .append("svg:path")
            .attr('class', function(d) {
                return (d.source.type == 1) ? 'link rolePath' : 'link permissionPath';
            })
            .attr("marker-start", function(d) {
                return "url(#marker)";
            });

        links
            .on('dblclick', deleteLink)
            .on("click", function(d, i) {
                d3.select("#infoItem").html(JSON.stringify(d));
            });

        links.exit().remove();
    };

    var setNodes = function(data) {
        var nodes = nodesGroup.selectAll("g.node")
            .data(data, function(d) {
                return d.name;
            });

        var group = nodes.enter()
            .append('g')
            .attr("class", "node");

        group.append('rect')
            .attr('class', function(d) {
                return (d.type == 1) ? 'icon roleIcon' : 'icon permissionIcon';
            })
            .attr("x", -rectW / 2)
            .attr("y", -rectH / 2)
            .attr("width", rectW)
            .attr("height", rectH);

        group.append("svg:text")
            .attr("class", "nodetext")
            .text(function(d, i) {
                return d.name + '(' + i + ')';
            }).style("text-anchor", "middle");


        group.call(node_drag)
            .on("click", function(d, i) {
                d3.select("#infoItem").html(JSON.stringify(d));
            })
            .on("dblclick", function(d) {
                document.getElementById("itemform-type").value = d.type;
                document.getElementById("itemform-oldname").value = d.name;
                document.getElementById("itemform-name").value = d.name;
                document.getElementById("itemform-description").value = d.description ? d.value : '';
                document.getElementById("itemform-data").value = d.data ? d.data : '';
                document.getElementById("itemform-rulename").value = d.rulename ? d.rulename : '';
            });

        nodes.exit().remove();
    };

    var refreshGraph = function() {
        setLinks(json.links);
        setNodes(json.nodes);
    };

    var node_drag = d3.behavior.drag()
        .on("dragstart", dragstart)
        .on("drag", dragmove)
        .on("dragend", dragend);

    var dragTarget;

    var addLink = function(sourceIndex, targetIndex) {
        var isInside = false;
        var cross = false;

        json.links.forEach(function(d) {
            if (d.source.index == sourceIndex && d.target.index == targetIndex) {
                isInside = true;
            }
            if (d.target.index == sourceIndex && d.source.index == targetIndex) {
                isInside = true;
                cross = true;
            }
        });

        if (!isInside) {

            $.post("index.php?r=rbac/default/add-child", {
                "source": force.nodes()[sourceIndex],
                "target": force.nodes()[targetIndex]
            }).success(function(data) {
                console.log(data);
            });

            json.links.push({
                "source": force.nodes()[sourceIndex],
                "target": force.nodes()[targetIndex]
            });

            force.stop();

            setLinks(json.links);

            force.start();
        }
    };

    function deleteLink(datum, index) {
        if (confirm("Are you sure?")) {

            $.post("index.php?r=rbac/default/remove-child", {
                "source": json.links[index].source,
                "target": json.links[index].target
            }).success(function(data) {
                console.log(data);
            });

            json.links.splice(index, 1);

            force.stop();

            setLinks(json.links);

            force.start();
        }
    }

    function dragstart(d, i) {
        zoomListener.on('zoom', null);
    }

    function dragmove(d, i) {
        dragTarget = null;
        d.px += d3.event.dx;
        d.py += d3.event.dy;
        d.x += d3.event.dx;
        d.y += d3.event.dy;

        d3.selectAll(".scopeCircle").remove();

        force.nodes().forEach(function(target) {
            if (target.name !== d.name) {
                if (Math.sqrt(Math.pow((target.x - d.x), 2) + Math.pow((target.y - d.y), 2)) < 60) {
                    dragTarget = target;
                    var selector = d3.selectAll("g.node").filter(function(d) {
                        return d.name === target.name;
                    });
                    selector.append("svg:circle").attr("r", rectW).attr("class", "scopeCircle");
                }
            }
        });
        tick(d3.event);
    }

    function dragend(d, i) {
        if (dragTarget != undefined) {
            addLink(dragTarget.index, i);
            d3.selectAll(".scopeCircle").remove();
            d.fixed = false;
        }
        else {
            d.fixed = true;
        }
        zoomListener.on("zoom", zoom);
    }

    function tick(event) {

        var nodesTick = d3.selectAll(".node");
        var linksTick = d3.selectAll("path.link")

        if (event.type !== "drag") {

            json.nodes[0].x = w / 2;
            json.nodes[0].y = rectH * 1.05;

            // json.links.forEach(function(d, i) {
            //     while (d.target.y - h / 20 < d.source.y) {
            //         d.target.y += h / 10;
            //         d.source.fixed = true;
            //     }
            // });

            if (event.alpha < 0.09) {
                force.stop();
            }
            else {
                d3.select("#status").html("Calculate" + event.alpha);
            }
        }

        nodesTick.attr("transform", function(d) {
            return "translate(" + d.x + "," + d.y + ")";
        });

        var childs = [];

        linksTick.attr("d", function(d) {

            if (childs[d.source.name] == undefined) {

                childs[d.source.name] = {
                    "northwest": [],
                    "northeast": [],
                    "southeast": [],
                    "southwest": [],
                };
            }

            if (d.source.y < d.target.y) {
                if (d.source.x < d.target.x) {
                     childs[d.source.name].southeast.push(d.target);
                } else {
                     childs[d.source.name].southwest.push(d.target);
                }
            }
            else {
                 if (d.source.x < d.target.x) {
                     childs[d.source.name].northeast.push(d.target);
                } else {
                     childs[d.source.name].northwest.push(d.target);
                }
            }

            var x1 = d.source.x,
                x2 = d.target.x,
                y1 = d.source.y,
                y2 = d.target.y,
                dy = rectH / 2,
                dx = rectW / 2;
                
            console.log(childs);
            

            if (d.source.y < d.target.y) {

                y1 += dy;
                y2 -= dy;

                var Mx,Vx;

                if (d.source.x < d.target.x) {
                    Mx = x1 + rectW / 20 * childs[d.source.name].southeast.length;
                    Vx = y1 + dy / 2 * childs[d.source.name].southeast.length;
                }
                else {
                    Mx = x1 - rectW / 20 * childs[d.source.name].southwest.length;
                    Vx = y1 + dy / 2 * childs[d.source.name].southwest.length;
                }

                return [
                    "M", Mx, y1+10,
                    "V", Vx,
                    "H", x2,
                    "V", y2
                ].join(" ");
                
            } else {
                
                y1 -= dy;
                y2 += dy;

                var Mx,Vx;

                if (d.source.x < d.target.x) {
                    Mx = x1 + rectW / 20 * childs[d.source.name].northeast.length;
                    Vx = y1 - dy / 2 * childs[d.source.name].northeast.length;
                }
                else {
                    Mx = x1 - rectW / 20 * childs[d.source.name].northwest.length;
                    Vx = y1 - dy / 2 * childs[d.source.name].northwest.length;
                }

                return [
                    "M", Mx, y1-10,
                    "V", Vx,
                    "H", x2,
                    "V", y2
                ].join(" ");
            }
        });
    }

    d3.select('#submitForm').on('click', function() {
        $.post("index.php?r=rbac/default/save-item", $("#mainForm").serialize())
            .success(function(data) {
                var node = JSON.parse(data);

                if (node.oldName) {
                    json.nodes.forEach(function(n, i) {
                        if (n.name === node.oldName) {
                            json.nodes[i].name = node.name;
                            json.nodes[i].description = node.description;
                            json.nodes[i].rulename = node.rulename;
                            json.nodes[i].type = node.type;
                            json.nodes[i].data = node.data;
                        }
                    });
                }
                else {
                    json.nodes.push(node);
                }

                force.stop();
                setNodes(json.nodes);
                d3.selectAll("text.nodetext").text(function(d, i) {
                    return (d.name === node.oldName ? node.name : d.name) + " (" + i + ")";
                });
                force.start();
            });
    });

    d3.select('#deleteForm').on('click', function() {
        $.post("index.php?r=rbac/default/delete-item", $("#mainForm").serialize())
            .success(function(data) {
                window.location.reload();
            });
    });

    refreshGraph();
});
