<style>
/* ============================================ */
/* CORE REPORT CARD STYLES                      */
/* ============================================ */
.report-card-wrapper {
    max-width: 1000px;
    margin: 0 auto;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
    background: white;
}

.report-card-content {
    padding: 15px 20px;
    font-family: 'Poppins', Arial, sans-serif;
    color: #1a1a1a;
    background: white;
    font-size: 10px; /* Base font size */
    line-height: 1.3;
}

/* ============ HEADER BANNER ============ */
.rc-header-banner {
    display: grid;
    grid-template-columns: 90px 1fr 80px;
    gap: 15px;
    align-items: center;
    background: var(--report-header-color, #8b1a1a);
    color: var(--report-text-color, #ffffff);
    padding: 10px 15px;
    margin-bottom: 8px;
    border-radius: 4px;
}
.rc-header-logo img {
    max-width: 80px;
    max-height: 80px;
    object-fit: contain;
    background: white;
    border-radius: 50%;
    padding: 3px;
    border: 2px solid white;
}
.rc-logo-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: white;
    color: var(--report-header-color, #8b1a1a);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 8px;
    border: 2px solid white;
    line-height: 1.1;
}
.rc-header-text { text-align: center; }
.rc-school-name {
    font-size: 18px;
    font-weight: 700;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
    line-height: 1.1;
}
.rc-school-contact { font-size: 10px; font-weight: 500; }
.rc-school-contact-row { font-size: 10px; font-weight: 500; }
.rc-sep { margin: 0 5px; opacity: 0.7; }
.rc-school-motto { font-size: 9px; font-style: italic; opacity: 0.9; margin-top: 2px; }

.rc-header-photo img,
.rc-photo-placeholder {
    width: 75px;
    height: 75px;
    object-fit: cover;
    border: 2px solid white;
    border-radius: 3px;
    background: #f0f0f0;
}
.rc-photo-placeholder {
    display: flex; align-items: center; justify-content: center;
    color: #999; font-size: 1.5rem;
}

/* ============ STUDENT INFO TABLE ============ */
.rc-info-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
    border: 1px solid #ccc;
    font-size: 9px;
}
.rc-info-table td {
    border: 1px solid #ccc;
    padding: 3px 6px;
    vertical-align: middle;
}
.rc-info-label {
    background: #f5f5f5;
    font-weight: 700;
    width: 14%;
    color: #333;
    font-size: 8px;
}
.rc-info-value { width: 19%; font-weight: 600; }

/* ============ SECTION TITLES ============ */
.rc-section-title {
    background: var(--report-header-color, #8b1a1a);
    color: var(--report-text-color, #ffffff);
    padding: 3px 8px;
    font-weight: 700;
    font-size: 10px;
    letter-spacing: 0.5px;
    margin: 8px 0 0;
    border-radius: 2px 2px 0 0;
}

/* ============ RESULT ANALYSIS ============ */
.rc-analysis-row {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 5px;
    padding: 6px 0;
    border: 1px solid #ccc;
    border-top: none;
    background: #fafafa;
}
.rc-analysis-box {
    text-align: center;
    border-right: 1px dashed #ddd;
    padding: 0 3px;
}
.rc-analysis-box:last-child { border-right: none; }
.rc-analysis-label {
    font-size: 7px;
    text-transform: uppercase;
    color: #666;
    letter-spacing: 0.3px;
    margin-bottom: 2px;
    font-weight: 600;
}
.rc-analysis-value {
    font-size: 11px;
    font-weight: 700;
    color: var(--report-header-color, #8b1a1a);
}

/* ============ SCORES TABLE ============ */
.rc-scores-table {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #ccc;
    font-size: 8.5px;
}
.rc-scores-table th {
    background: var(--report-header-color, #8b1a1a);
    color: var(--report-text-color, #ffffff);
    padding: 3px 2px;
    text-align: center;
    font-weight: 600;
    font-size: 8px;
    border: 1px solid rgba(0,0,0,0.2);
    line-height: 1.1;
}
.rc-th-subject { text-align: left !important; padding-left: 6px !important; }
.rc-th-ca, .rc-th-exam { background: var(--report-header-color, #8b1a1a); filter: brightness(1.1); }
.rc-th-grade, .rc-th-remark { background: rgba(0,0,0,0.2); }

.rc-scores-table td {
    border: 1px solid #ddd;
    padding: 2px 3px;
    font-size: 9px;
}
.rc-td-subject {
    font-weight: 700;
    padding-left: 6px !important;
    background: #fafafa;
    font-size: 8.5px;
}
.rc-td-num { text-align: center; font-weight: 500; }
.rc-td-grade {
    text-align: center;
    font-weight: 700;
    color: var(--report-header-color, #8b1a1a);
    font-size: 9px;
}
.rc-td-remark {
    text-align: center;
    font-weight: 600;
    font-size: 8px;
    color: var(--report-header-color, #8b1a1a);
    text-transform: uppercase;
}

/* ============ UNATTEMPTED ROWS ============ */
.rc-row-unattempted { background: #fafafa !important; opacity: 0.7; }
.rc-row-unattempted .rc-td-subject { color: #666; font-style: italic; }
.rc-td-dash { color: #b0b0b0 !important; font-style: italic; }
.rc-td-remark.rc-td-dash { color: #b0b0b0 !important; text-transform: none; font-weight: 400; }

/* ============ TRAITS (2 Columns) ============ */
.rc-traits-wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-top: 8px;
}
.rc-traits-col { border: 1px solid #ccc; }
.rc-traits-header {
    padding: 3px 8px;
    background: var(--report-header-color, #8b1a1a);
    color: var(--report-text-color, #ffffff);
    font-weight: 700;
    font-size: 9px;
}
.rc-traits-table { width: 100%; border-collapse: collapse; font-size: 8.5px; }
.rc-traits-table th {
    background: #f5f5f5; padding: 2px;
    text-align: center; font-weight: 600; font-size: 8px;
    border: 1px solid #ddd; width: 9%;
}
.rc-traits-table th:first-child { width: 55%; text-align: left; padding-left: 6px; }
.rc-trait-name { padding: 2px 6px; border: 1px solid #ddd; font-size: 8.5px; }
.rc-trait-cell { text-align: center; border: 1px solid #ddd; padding: 2px 0; }
.rc-trait-check { color: var(--report-header-color, #8b1a1a); font-weight: 900; font-size: 11px; }
.rc-trait-box { display: inline-block; width: 7px; height: 7px; border: 1px solid #ccc; }

/* ============ ATTENDANCE ============ */
.rc-attendance-wrapper { display: flex; justify-content: flex-end; margin-top: 8px; }
.rc-attendance-table {
    border-collapse: collapse; border: 1px solid #ccc;
    font-size: 9px; min-width: 250px;
}
.rc-attendance-table td { border: 1px solid #ccc; padding: 3px 8px; }
.rc-attendance-label {
    background: var(--report-header-color, #8b1a1a);
    color: var(--report-text-color, #ffffff);
    font-weight: 700; font-size: 9px;
}
.rc-attendance-value { text-align: right; font-weight: 700; color: var(--report-header-color, #8b1a1a); min-width: 50px; }

/* ============ COMMENTS ============ */
.rc-comments-table {
    width: 100%; border-collapse: collapse;
    margin-top: 3px; border: 1px solid #ccc; font-size: 9px;
}
.rc-comments-table td { border: 1px solid #ccc; padding: 4px 6px; vertical-align: middle; }
.rc-comments-label {
    background: #f5f5f5; font-weight: 700; width: 15%; font-size: 8.5px;
}
.rc-comments-cell { width: 65%; }
.rc-comments-sign { width: 20%; text-align: center; }
.rc-comment-row { margin-bottom: 2px; line-height: 1.3; }
.rc-signature-label { font-size: 7px; color: #666; font-weight: 600; }
.rc-signature-space { height: 20px; border-bottom: 1px solid #999; }


/* ============================================ */
/* THE MAGIC: PRINT-TO-1-PAGE RULES             */
/* ============================================ */
@media print {
    /* Hide everything else */
    .navbar, .sidebar, .no-print, .btn, .card-header, .page-header { 
        display: none !important; 
    }
    
    /* Reset page setup */
    @page {
        size: A4 portrait;
        margin: 8mm 10mm; /* Tight margins to fit everything */
    }
    
    body, html {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        background: white;
    }

    /* Flatten the layout so it uses the full page */
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    .container-fluid, .row, .col-lg-12, .card, .card-body {
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
    }

    /* Ensure the report card fills the page */
    .report-card-wrapper {
        max-width: 100% !important;
        width: 100% !important;
        box-shadow: none !important;
    }
    
    .report-card-content {
        padding: 0 !important;
        font-size: 9px !important; /* Force smaller base font for print */
    }

    /* Scale down specific tables for print */
    .rc-scores-table { font-size: 8px !important; }
    .rc-scores-table th { font-size: 7.5px !important; padding: 2px 1px !important; }
    .rc-scores-table td { font-size: 8px !important; padding: 1px 2px !important; }
    .rc-td-subject { font-size: 8px !important; }
    
    .rc-traits-table { font-size: 7.5px !important; }
    .rc-trait-name, .rc-trait-cell { padding: 1px !important; }
    
    .rc-info-table { font-size: 8px !important; margin-bottom: 4px !important; }
    .rc-info-table td { padding: 2px 4px !important; }
    
    .rc-analysis-row { padding: 4px 0 !important; margin-bottom: 0 !important; }
    .rc-section-title { padding: 2px 6px !important; margin-top: 6px !important; }
    .rc-header-banner { padding: 8px !important; margin-bottom: 5px !important; }
    .rc-school-name { font-size: 16px !important; }
    .rc-header-logo img, .rc-logo-placeholder { width: 60px !important; height: 60px !important; }
    .rc-header-photo img, .rc-photo-placeholder { width: 60px !important; height: 60px !important; }

    /* Colors MUST be printed */
    .rc-header-banner, .rc-section-title, .rc-scores-table th, 
    .rc-traits-header, .rc-attendance-label, .rc-info-label,
    .rc-td-grade, .rc-trait-check {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    /* Prevent page breaks inside important blocks */
    .rc-scores-table, .rc-traits-wrapper, .rc-comments-table, .rc-header-banner {
        page-break-inside: avoid;
    }
}

/* ============================================ */
/* MOBILE ZOOMABLE VIEW (No longer squished)    */
/* ============================================ */
@media (max-width: 767.98px) {
    .report-card-wrapper {
        overflow-x: auto; /* Allows horizontal scroll */
        -webkit-overflow-scrolling: touch; /* Smooth scroll on iOS */
        padding-bottom: 10px;
    }
    
    .report-card-content {
        width: 800px; /* Force desktop width on mobile */
        min-width: 800px;
        transform-origin: top left;
    }
    
    /* Hide the mobile-specific card view, we are using the actual card now */
    .report-mobile-view { display: none !important; }
}
</style>