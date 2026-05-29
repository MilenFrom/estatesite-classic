/*
 * Houzez Invoice JS
 */
(function ($) {
    'use strict';

    var invoice_title = houzez_vars.invoice_title;

    /*--------------------------------------------------------------------------
     *  Invoice Filter
     * -------------------------------------------------------------------------*/
    $('#invoice_status, #invoice_type').on('change', function () {
        houzez_invoices_update_url();
    });

    $('#startDate, #endDate').on('change', function () {
        houzez_invoices_update_url();
    });

    var houzez_invoices_update_url = function () {
        var inv_status = $('#invoice_status').val(),
            inv_type = $('#invoice_type').val(),
            startDate = $('#startDate').val(),
            endDate = $('#endDate').val();

        // Construct the query string
        var queryStringParts = [];

        if (inv_status) {
            queryStringParts.push(
                'invoice_status=' + encodeURIComponent(inv_status)
            );
        }

        if (inv_type) {
            queryStringParts.push(
                'invoice_type=' + encodeURIComponent(inv_type)
            );
        }

        if (startDate) {
            queryStringParts.push('startDate=' + encodeURIComponent(startDate));
        }

        if (endDate) {
            queryStringParts.push('endDate=' + encodeURIComponent(endDate));
        }

        var queryString = queryStringParts.join('&');

        // Construct new URL without the page part
        var newUrl = $('#invoices_page').val();

        if (queryString) {
            newUrl += '?' + queryString;
        }

        // Append the query string to the current URL and reload the page
        window.location.href = newUrl;
    };

    $(document).ready(function () {
        // Print invoice
        $('.invoice-print').on('click', function (e) {
            e.preventDefault();
            var invoiceID = $(this).data('invoice-id');
            var modalContent = $(
                '#invoice-modal-' + invoiceID + ' .modal-content'
            ).clone();

            // Remove the modal header and footer before printing
            modalContent.find('.modal-header, .modal-footer').remove();

            // Expand modal body to full width
            modalContent.find('.modal-body').css('padding', '30px');

            var printWindow = window.open('', '_blank');
            printWindow.document.write(
                '<html><head><title>' +
                    invoice_title +
                    ' #' +
                    invoiceID +
                    '</title>'
            );

            // Include all necessary stylesheets
            printWindow.document.write(
                '<link rel="stylesheet" href="' +
                    houzez_vars.css_url_bootstrap +
                    '" type="text/css" />'
            );
            if (houzez_vars.css_dashboard) {
                printWindow.document.write(
                    '<link rel="stylesheet" href="' +
                        houzez_vars.css_dashboard +
                        '" type="text/css" />'
                );
            }

            // Add custom print styles
            printWindow.document.write('<style>');
            printWindow.document.write(`
                body { 
                    padding: 30px; 
                    font-family: 'Open Sans', sans-serif;
                    line-height: 1.5;
                    color: #333;
                    background: #fff;
                }
                .modal-content {
                    position: relative;
                    width: 100%;
                    max-width: 800px;
                    margin: 0 auto;
                    background-color: #fff;
                    border: 1px solid #e5e5e5;
                    border-radius: 4px;
                    box-shadow: none;
                    padding: 20px;
                }
                .modal-body {
                    padding: 0;
                }
                .invoice-details {
                    border: 1px solid #dce0e0;
                    padding: 20px !important;
                    margin-bottom: 20px !important;
                    border-radius: 4px;
                }
                .invoice-description {
                    background-color: #f8f9fa;
                    padding: 15px !important;
                    border-radius: 4px;
                }
                .fw-bold {
                    font-weight: 700 !important;
                }
                .dashboard-label {
                    display: inline-block;
                    padding: 4px 8px;
                    border-radius: 4px;
                }
                .bg-success {
                    background-color: #28a745 !important;
                    color: white !important;
                }
                .bg-danger {
                    background-color: #dc3545 !important;
                    color: white !important;
                }
                .d-flex {
                    display: flex !important;
                }
                .justify-content-between {
                    justify-content: space-between !important;
                }
                .mb-3 {
                    margin-bottom: 1rem !important;
                }
                .text-end {
                    text-align: right !important;
                }
                .invoice-bill ul {
                    list-style: none;
                    padding: 0;
                    margin: 0;
                }
                @media print {
                    body { 
                        padding: 0;
                    }
                    .modal-content {
                        border: none;
                        box-shadow: none;
                    }
                }
            `);
            printWindow.document.write('</style>');

            printWindow.document.write('</head><body>');
            printWindow.document.write('<div class="modal-content">');
            printWindow.document.write(modalContent.html());
            printWindow.document.write('</div>');
            printWindow.document.write('</body></html>');

            setTimeout(function () {
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }, 500);
        });
    });
})(jQuery);
