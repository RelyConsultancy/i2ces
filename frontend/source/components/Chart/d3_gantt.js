import d3 from 'd3'

window.d3 = d3
/**
 * @author Dimitry Kudrayvtsev
 * @version 2.1
 */

d3.gantt = function (container, { data, palette, tickFormat }) {
  data = data.map((item) => {
    item.date_start = new Date(item.date_start)
    item.date_end = new Date(item.date_end)

    return item
  })

  const labels = data.map(i => i.label)
  const margin = { top: 30, right: 20, bottom: 0, left: 130 }

  let timeDomainMode = 'fit' // fixed or fit
  let timeDomainStart = data
    .map(i => i.date_start)
    .sort((a, b) => (a - b))
    .shift()

  let timeDomainEnd = data
    .map(i => i.date_end)
    .sort((a, b) => (a - b))
    .pop()


  const draw = () => {
    let height = container.offsetHeight - margin.top - margin.bottom
    let width = container.offsetWidth - margin.right - margin.left

    const xScale = d3.time.scale()
      .domain([timeDomainStart, timeDomainEnd])
      .nice(d3.time.week)
      .range([0, width])
      .clamp(true)

    const yScale = d3.scale.ordinal()
      .domain(labels)
      .rangeBands([0, height - margin.top - margin.bottom])

    const xAxis = d3.svg.axis()
      .scale(xScale)
      .orient('top')
      .tickFormat(d3.time.format(tickFormat))
      .tickPadding(10)
      .tickSize(0)

    const yAxis = d3.svg.axis()
      .scale(yScale)
      .orient('left')
      .tickPadding(5)
      .tickSize(0)

    if (timeDomainMode === 'fit') {
      data.sort(function(a, b) {
        return a.date_end - b.date_end
      })
      timeDomainEnd = data[data.length - 1].date_end
      data.sort(function(a, b) {
        return a.date_start - b.date_start
      })
      timeDomainStart = data[0].date_start
    }


    var svg = d3.select(container)
      .html('')
      .append('svg')
      .attr('class', 'chart')
      .attr('width', width + margin.left + margin.right)
      .attr('height', height + margin.top + margin.bottom)
      .append('g')
      .attr('class', 'gantt-chart')
      .attr('width', width + margin.left + margin.right)
      .attr('height', height + margin.top + margin.bottom)
      .attr('transform', `translate(${margin.left}, ${margin.top})`)

    svg.selectAll('.chart')
      .data(data)
      .enter()
      .append('rect')
      .attr('fill', item => palette[item.type])
      .attr('transform', (item) => (
        `translate(${xScale(item.date_start)}, ${yScale(item.label)})`
      ))
      .attr('height', item => yScale.rangeBand())
      .attr('width', (item) => (
        xScale(item.date_end) - xScale(item.date_start)
      ))

    svg.append('g')
      .attr('class', 'axis axis_x')
      .attr('font-size', '.75em')
      .attr('fill', '#5A5A5A')
      .transition()
      .call(xAxis)

    svg.append('g')
      .attr('class', 'axis axis_y')
      .attr('transform', `translate(-10, 0)`)
      .transition()
      .call(yAxis)

    // grid
    svg.selectAll('line.horizontal_grid')
      .data(data)
      .enter()
      .append('line')
      .attr({
        'class': 'horizontal_grid',
        'x1': 0,
        'x2': width,
        'y1': (item => yScale(item.label)),
        'y2': (item => yScale(item.label)),
        'fill': 'none',
        'stroke': '#ecf0f1',
        'stroke-width': '1px',
        'shape-rendering': 'crispEdges',
      })
  }

  draw()

  // responsive
  d3.select(window).on('resize', draw)
}

