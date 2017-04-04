import d3 from 'd3'
import moment from 'moment'

const fmtTime = d3.time.format('%Y-%m-%dT%H:%M:%S')

const getDayOfYear = (date) => {
  const start = new Date(date.getFullYear(), 0, 0)
  const diff = date - start
  const oneDay = 1000 * 60 * 60 * 24

  return Math.floor(diff / oneDay)
}


d3.gantt = function (container, { data, palette, tickFormat }) {
  data = data.map((item) => (
    Object.assign({}, item, {
      date_start: fmtTime.parse(item.date_start), 
      date_end: fmtTime.parse(item.date_end),
    })
  )) 
    
  console.log("DATA", data)
    
  const margin = { top: 30, right: 20, bottom: 0, left: 130 }

  const timeDomainStart = data
    .map(i => i.date_start)
    .sort((a, b) => (a - b))
    .shift()

  const timeDomainEnd = data
    .map(i => i.date_end)
    .sort((a, b) => (a - b))
    .pop()
  
  const from = getDayOfYear(timeDomainStart)
  const to = getDayOfYear(timeDomainEnd)
  const days = to - from
  
  const test = moment(data.date_end).diff(moment(data.date_start), 'days')
  
  console.log("TIME TEST", test)
  
  const draw = () => {
    const height = container.offsetHeight - margin.top - margin.bottom
    const width = container.offsetWidth - margin.right - margin.left

    const xScale = d3.time.scale()
      .domain([timeDomainStart, timeDomainEnd])
      .nice(d3.time[days <= 14 ? 'day' : 'week'])
      .rangeRound([0, width])

    const yScale = d3.scale.ordinal()
      .domain(data.map(i => i.label))
      .rangeBands([0, height - margin.top - margin.bottom])

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

    const xAxis = d3.svg.axis()
      .scale(xScale)
      .orient('top')
      .tickFormat(d3.time.format(tickFormat))
      .tickPadding(10)
      .tickSize(0)
      .ticks(d3.time[days <= 14 ? 'day' : 'week'])

    svg.append('g')
      .attr('class', 'axis axis_x')
      .attr('font-size', '.75em')
      .attr('fill', '#5A5A5A')
      .transition()
      .call(xAxis)

    const yAxis = d3.svg.axis()
      .scale(yScale)
      .orient('left')
      .tickPadding(5)
      .tickSize(0)

    svg.append('g')
      .attr('class', 'axis axis_y')
      .attr('transform', `translate(-10, 0)`)
      .transition()
      .call(yAxis)

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

    setTimeout(function () {
      svg.selectAll('.axis_x .tick > line')
        .attr({
          'stroke': '#C9C9C9',
          'y2': '-4',
        })
    }, 0)
  }

  draw()

  // resize
  d3.select(window).on('resize', draw)
}