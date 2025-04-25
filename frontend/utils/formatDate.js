import moment from 'moment'

/**
 * Format a date to a readable string
 * @param {string} date - The date to format
 * @param {string} format - The format to use (optional)
 * @returns {string} - The formatted date
 */
export function formatDate(date, format = 'MMM D, YYYY') {
  return moment(date).format(format)
}

/**
 * Format a date to include time
 * @param {string} date - The date to format
 * @returns {string} - The formatted date with time
 */
export function formatDateTime(date) {
  return moment(date).format('MMM D, YYYY h:mm A')
}

/**
 * Get current date in YYYY-MM-DD format
 * @returns {string} - Today's date
 */
export function getCurrentDate() {
  return moment().format('YYYY-MM-DD')
}

/**
 * Get current month in YYYY-MM format
 * @returns {string} - Current month
 */
export function getCurrentMonth() {
  return moment().format('YYYY-MM')
}

/**
 * Format month-year string
 * @param {string} monthStr - Month in YYYY-MM format
 * @returns {string} - Formatted month and year
 */
export function formatMonthYear(monthStr) {
  return moment(monthStr).format('MMMM YYYY')
}
