import jsPDF from 'jspdf'
import exportFromJSON from 'export-from-json'
import { formatMonthYear } from './formatDate'

export const exportToPDF = (expenses, month) => {
  const doc = new jsPDF()
  const pageWidth = doc.internal.pageSize.width

  // Title
  doc.setFontSize(20)
  doc.text('Expense Report', pageWidth / 2, 20, { align: 'center' })

  // Month
  doc.setFontSize(14)
  doc.text(`Month: ${formatMonthYear(month)}`, 20, 35)

  // Table headers
  doc.setFontSize(12)
  const headers = ['Name', 'Amount (₹)']
  let y = 50

  doc.text(headers[0], 20, y)
  doc.text(headers[1], pageWidth - 60, y)

  y += 10
  doc.line(20, y, pageWidth - 20, y)
  y += 10

  // Expense items
  expenses.forEach((expense) => {
    if (y > 270) {
      // Check if we need a new page
      doc.addPage()
      y = 20
    }

    doc.text(expense.name, 20, y)
    doc.text(expense.amount.toString(), pageWidth - 60, y)
    y += 10
  })

  // Total
  const total = expenses.reduce((sum, expense) => sum + expense.amount, 0)
  y += 5
  doc.line(20, y, pageWidth - 20, y)
  y += 10
  doc.setFont(undefined, 'bold')
  doc.text('Total:', 20, y)
  doc.text(`₹${total}`, pageWidth - 60, y)

  // Download PDF
  doc.save(`expense-report-${month}.pdf`)
}

export const exportToCSV = (expenses, month) => {
  const data = expenses.map((expense) => ({
    Name: expense.name,
    Amount: expense.amount,
    Date: expense.date,
  }))

  const fileName = `expense-report-${month}`
  const exportType = 'csv'

  exportFromJSON({ data, fileName, exportType })
}
