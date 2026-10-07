<!-- resources/views/partials/report-card-styles.blade.php -->
<style>
/* ============ SCREEN STYLES ============ */
.report-card-wrapper {
    max-width: 900px;
    margin: 0 auto;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
}
.report-card-content {
    padding: 20px 25px;
    font-family: 'Poppins', sans-serif;
    color: #1a1a1a;
    background: white;
}
.report-header {
    text-align: center;
    border-bottom: 2px solid #1a1a1a;
    padding-bottom: 8px;
    margin-bottom: 12px;
}
.report-school-name {
    font-size: 18px;
    font-weight: 700;
    color: #1e40af;
    letter-spacing: 0.5px;
}
.report-title {
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 2px;
    margin-top: 2px;
}
.report-term {
    font-size: 11px;
    color: #555;
    margin-top: 2px;
    font-style: italic;
}
.report-info { margin-bottom: 10px; }
.info-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
}
.info-table tr td { padding: 3px 5px; vertical-align: middle; }
.info-label { width: 12%; color: #555; font-weight: 500; }
.info-value { width: 38%; }
.report-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10px;
    margin-bottom: 8px;
}
.report-table thead th {
    background: #1e40af;
    color: white;
    padding: 5px 4px;
    text-align: left;
    font-weight: 600;
    font-size: 10px;
    border: 1px solid #1e40af;
}
.report-table tbody td {
    padding: 3px 4px;
    border: 1px solid #d0d5dd;
    vertical-align: middle;
}
.report-table tbody tr:nth-child(even) { background: #f8f9fc; }
.report-table tbody td .text-muted { font-size: 8px; color: #888 !important; }
.report-table tfoot td {
    padding: 5px 4px;
    border: 1px solid #1e40af;
    background: #e7f0ff;
    font-size: 10px;
}
.report-total strong { color: #1e40af; }
.grade {
    display: inline-block;
    padding: 1px 6px;
    border-radius: 3px;
    font-weight: 700;
    font-size: 9px;
    min-width: 20px;
    text-align: center;
}
.grade-a { background: #198754; color: white; }
.grade-b { background: #0d6efd; color: white; }
.grade-c { background: #0dcaf0; color: white; }
.grade-d { background: #ffc107; color: #000; }
.grade-e { background: #fd7e14; color: white; }
.grade-f { background: #dc3545; color: white; }
.report-summary { display: flex; gap: 6px; margin-bottom: 8px; }
.summary-box {
    flex: 1;
    text-align: center;
    padding: 5px 3px;
    background: #f8f9fc;
    border: 1px solid #d0d5dd;
    border-radius: 4px;
}
.summary-label {
    font-size: 8px;
    text-transform: uppercase;
    color: #666;
    letter-spacing: 0.5px;
    margin-bottom: 1px;
}
.summary-value { font-size: 13px; font-weight: 700; color: #1e40af; }
.summary-remark { font-size: 10px !important; }
.report-grading {
    text-align: center;
    font-size: 9px;
    padding: 5px 8px;
    background: #f0f4ff;
    border-radius: 4px;
    border: 1px solid #d0d5dd;
    margin-bottom: 12px;
    line-height: 1.5;
}
.grading-title { font-weight: 700; color: #1e40af; margin-right: 6px; }
.grading-item { margin-right: 8px; color: #444; white-space: nowrap; }
.report-footer {
    display: flex;
    justify-content: space-between;
    margin-top: 15px;
    padding-top: 8px;
}
.footer-left, .footer-right { width: 40%; }
.signature-line { text-align: center; }
.signature-space {
    border-bottom: 1px solid #333;
    height: 25px;
    margin-bottom: 3px;
}
.signature-label { font-size: 9px; color: #555; font-weight: 500; }
.report-generation-info {
    text-align: center;
    font-size: 8px;
    color: #999;
    margin-top: 10px;
    padding-top: 5px;
    border-top: 1px dashed #ddd;
}
@media print {
    body * { visibility: hidden; }
    .report-card-content, .report-card-content * { visibility: visible; }
    .report-card-content {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 8mm;
        box-shadow: none !important;
    }
    .navbar, .sidebar, .no-print, .btn, .card-header { display: none !important; }
    .report-card-wrapper {
        box-shadow: none !important;
        max-width: 100% !important;
        margin: 0 !important;
        border: none !important;
    }
    .col-lg-12 {
        max-width: 100% !important;
        flex: 0 0 100% !important;
        padding: 0 !important;
    }
    .card, .card-body {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    @page { size: A4; margin: 8mm; }
    .report-table, .report-summary, .report-footer { page-break-inside: avoid; }
    .report-card-content { font-size: 10px; }
    .report-table { font-size: 9px; }
    .report-table thead th { font-size: 9px; padding: 4px 3px; }
    .report-table tbody td { padding: 2px 3px; }
    .report-school-name { font-size: 16px; }
    .report-title { font-size: 12px; }
    .report-term { font-size: 10px; }
    .info-table { font-size: 10px; }
    .summary-value { font-size: 11px; }
    .report-grading { font-size: 8px; }
}
</style>