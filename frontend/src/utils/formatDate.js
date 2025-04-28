import moment from 'moment'

export const formatMonthYear = (monthStr) => {
  return moment(monthStr).format('MMMM YYYY')
}

export const formatDate = (date) => {
  return moment(date).format('MMM D, YYYY')
}
