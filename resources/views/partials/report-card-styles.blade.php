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

    /* Hide mobile view on print */
     .report-mobile-view { display: none !important; }
    .report-card-content > .report-header,
    .report-card-content > .report-info,
    .report-card-content > .report-table,
    .report-card-content > .report-summary,
    .report-card-content > .report-grading,
    .report-card-content > .report-footer,
    .report-card-content > .report-generation-info {
        display: block !important;
    }
}

 /* ============================================ */
/* MOBILE REPORT CARD VIEW                      */
/* ============================================ */
.report-mobile-view {
    padding: 12px;
    font-family: 'Poppins', sans-serif;
    color: #1a1a1a;
    background: white;
    font-size: 13px;
}

/* Header */
.mobile-report-header {
    text-align: center;
    padding-bottom: 12px;
    border-bottom: 2px solid #1e40af;
    margin-bottom: 12px;
}
.mobile-logo {
    max-height: 50px;
    max-width: 50px;
    object-fit: contain;
    margin-bottom: 6px;
}
.mobile-school-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e40af;
    line-height: 1.3;
}
.mobile-report-title {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 2px;
    margin-top: 4px;
}
.mobile-report-term {
    font-size: 0.75rem;
    color: #666;
    font-style: italic;
    margin-top: 2px;
}

/* Info Box */
.mobile-info-box {
    background: #f8f9fc;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 14px;
    border: 1px solid #e3e6f0;
}
.mobile-info-row {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    border-bottom: 1px dashed #e3e6f0;
    font-size: 0.85rem;
}
.mobile-info-row:last-child { border-bottom: none; }
.mobile-info-label {
    color: #666;
    font-weight: 500;
}
.mobile-info-value {
    font-weight: 600;
    text-align: right;
    max-width: 60%;
    word-break: break-word;
}

/* Subject Cards */
.mobile-subjects {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 14px;
}
.mobile-subject-card {
    background: white;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.mobile-subject-header {
    display: flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #1e40af, #4e73df);
    color: white;
    padding: 8px 12px;
    font-weight: 600;
    font-size: 0.9rem;
}
.mobile-subject-num {
    background: rgba(255,255,255,0.25);
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    flex-shrink: 0;
}
.mobile-subject-name {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Score Grid */
.mobile-subject-scores {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
    padding: 10px;
}
.mobile-score-box {
    text-align: center;
    padding: 6px 4px;
    background: #f8f9fc;
    border-radius: 6px;
    border: 1px solid #e9eef5;
}
.mobile-score-label {
    font-size: 0.65rem;
    text-transform: uppercase;
    color: #888;
    letter-spacing: 0.3px;
    margin-bottom: 2px;
}
.mobile-score-value {
    font-weight: 700;
    font-size: 0.9rem;
    color: #1e40af;
    line-height: 1.2;
}
.mobile-score-value small {
    font-size: 0.7rem;
    color: #999;
    font-weight: 400;
}

/* Subject Remark */
.mobile-subject-remark {
    padding: 8px 12px;
    background: #f0f4ff;
    border-top: 1px solid #e3e6f0;
    font-size: 0.78rem;
    color: #555;
}
.mobile-subject-remark i {
    color: #4e73df;
    margin-right: 4px;
}

/* Grand Total */
.mobile-grand-total {
    background: #f8f9fc;
    border-radius: 8px;
    padding: 10px 14px;
    margin-bottom: 14px;
    border: 1px solid #e3e6f0;
}
.mobile-grand-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 0;
    font-size: 0.85rem;
    border-bottom: 1px dashed #e3e6f0;
}
.mobile-grand-row:last-child { border-bottom: none; }
.mobile-grand-row span:first-child {
    color: #666;
    font-weight: 500;
}
.mobile-grand-highlight {
    background: #e7f0ff;
    margin: 4px -14px;
    padding: 10px 14px;
    border-bottom: none !important;
    border-top: 2px solid #1e40af;
    font-size: 0.95rem;
}
.mobile-grand-highlight span:first-child {
    color: #1e40af;
    font-weight: 700;
}
.mobile-grand-highlight strong {
    color: #1e40af;
    font-size: 1.05rem;
}

/* Grading */
.mobile-grading {
    background: #f0f4ff;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 14px;
    border: 1px solid #e3e6f0;
}
.mobile-grading-title {
    font-weight: 700;
    color: #1e40af;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: center;
    margin-bottom: 8px;
}
.mobile-grading-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
    font-size: 0.75rem;
    text-align: center;
}
.mobile-grading-grid span {
    padding: 4px 6px;
    background: white;
    border-radius: 5px;
    color: #444;
}
.mobile-grading-grid strong {
    color: #1e40af;
    margin-right: 3px;
}

/* Signatures */
.mobile-signatures {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-top: 18px;
    margin-bottom: 10px;
}
.mobile-signature-line {
    border-bottom: 1px solid #333;
    height: 30px;
    margin-bottom: 4px;
}
.mobile-signatures small {
    font-size: 0.68rem;
    color: #555;
    display: block;
    text-align: center;
}

/* Footer */
.mobile-footer-note {
    text-align: center;
    font-size: 0.7rem;
    color: #999;
    padding-top: 8px;
    border-top: 1px dashed #ddd;
}

/* Grade badges for mobile */
.mobile-subject-card .grade {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 700;
    font-size: 0.75rem;
    min-width: 28px;
    text-align: center;
}

/* Hide mobile view on tablets and up, hide desktop view on mobile */
@media (min-width: 768px) {
    .report-mobile-view { display: none !important; }
}
@media (max-width: 767.98px) {
    .report-card-content > .report-header,
    .report-card-content > .report-info,
    .report-card-content > .report-table,
    .report-card-content > .report-summary,
    .report-card-content > .report-grading,
    .report-card-content > .report-footer,
    .report-card-content > .report-generation-info {
        display: none !important;
    }
    
    /* Make the wrapper mobile-friendly */
    .report-card-wrapper {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-radius: 12px;
        overflow: hidden;
    }
    .report-card-content {
        padding: 0;
    }
}

/* Print: always use desktop layout */
/*
@media print {
    .report-mobile-view { display: none !important; }
    .report-card-content > .report-header,
    .report-card-content > .report-info,
    .report-card-content > .report-table,
    .report-card-content > .report-summary,
    .report-card-content > .report-grading,
    .report-card-content > .report-footer,
    .report-card-content > .report-generation-info {
        display: block !important;
    }
}  */

</style>