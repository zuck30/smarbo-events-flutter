import 'package:flutter/material.dart';
import 'package:smarbo_events/core/theme/app_theme.dart';
import 'package:smarbo_events/domain/entities/event.dart';
import 'package:smarbo_events/domain/entities/contribution.dart';
import 'package:printing/printing.dart';
import 'package:pdf/pdf.dart';
import 'package:pdf/widgets.dart' as pw;

class ReportGenerator {
  static Future<void> generateEventReport({
    required Event event,
    required List<Contribution> contributions,
  }) async {
    final pdf = pw.Document();

    pdf.addPage(
      pw.MultiPage(
        pageFormat: PdfPageFormat.a4,
        build: (pw.Context context) {
          double totalPromised = 0;
          double totalPaid = 0;
          for (var c in contributions) {
            totalPromised += c.promisedAmount;
            totalPaid += c.paidAmount;
          }

          return [
            pw.Header(
              level: 0,
              child: pw.Text(event.eventName.toUpperCase(), style: pw.TextStyle(fontSize: 24, fontWeight: pw.FontWeight.bold)),
            ),
            pw.SizedBox(height: 10),
            pw.Text('Event Type: ${event.eventType.toUpperCase()}'),
            pw.Text('Event Date: ${event.eventDate.toString().split(' ')[0]}'),
            pw.Text('Location: ${event.location}'),
            pw.SizedBox(height: 30),
            pw.Text('FINANCIAL SUMMARY', style: pw.TextStyle(fontSize: 18, fontWeight: pw.FontWeight.bold)),
            pw.SizedBox(height: 10),
            pw.Row(
              mainAxisAlignment: pw.MainAxisAlignment.spaceBetween,
              children: [
                pw.Text('Total Promised: TZS $totalPromised'),
                pw.Text('Total Paid: TZS $totalPaid'),
                pw.Text('Balance: TZS ${totalPromised - totalPaid}'),
              ],
            ),
            pw.SizedBox(height: 30),
            pw.Text('DETAILED CONTRIBUTIONS', style: pw.TextStyle(fontSize: 18, fontWeight: pw.FontWeight.bold)),
            pw.SizedBox(height: 10),
            pw.Table.fromTextArray(
              context: context,
              data: <List<String>>[
                <String>['Contributor', 'Phone', 'Promised', 'Paid', 'Balance', 'Status'],
                ...contributions.map((c) => [
                      c.contributorName,
                      c.phoneNumber ?? '-',
                      c.promisedAmount.toString(),
                      c.paidAmount.toString(),
                      c.balance.toString(),
                      c.status.toUpperCase(),
                    ]),
              ],
              headerStyle: pw.TextStyle(fontWeight: pw.FontWeight.bold),
              headerDecoration: const pw.BoxDecoration(color: PdfColors.grey300),
              cellHeight: 30,
              cellAlignments: {
                0: pw.Alignment.centerLeft,
                1: pw.Alignment.center,
                2: pw.Alignment.centerRight,
                3: pw.Alignment.centerRight,
                4: pw.Alignment.centerRight,
                5: pw.Alignment.center,
              },
            ),
          ];
        },
      ),
    );

    await Printing.layoutPdf(
      onLayout: (PdfPageFormat format) async => pdf.save(),
      name: '${event.eventName}_Report.pdf',
    );
  }

  static Future<void> generateAdminReport({
    required int totalEvents,
    required int totalOwners,
    required double totalPromised,
    required double totalPaid,
    required List<Event> events,
  }) async {
    final pdf = pw.Document();

    pdf.addPage(
      pw.MultiPage(
        pageFormat: PdfPageFormat.a4,
        build: (pw.Context context) {
          return [
            pw.Header(
              level: 0,
              child: pw.Text('SMARBO PLUSEVENT - SYSTEM REPORT', style: pw.TextStyle(fontSize: 24, fontWeight: pw.FontWeight.bold)),
            ),
            pw.SizedBox(height: 20),
            pw.Text('Generated on: ${DateTime.now().toString()}'),
            pw.SizedBox(height: 30),
            pw.Text('SYSTEM OVERVIEW', style: pw.TextStyle(fontSize: 18, fontWeight: pw.FontWeight.bold)),
            pw.SizedBox(height: 10),
            pw.Bullet(text: 'Total Events: $totalEvents'),
            pw.Bullet(text: 'Total Event Owners: $totalOwners'),
            pw.Bullet(text: 'Total Promised Amount: TZS $totalPromised'),
            pw.Bullet(text: 'Total Paid Amount: TZS $totalPaid'),
            pw.SizedBox(height: 30),
            pw.Text('ALL EVENTS LIST', style: pw.TextStyle(fontSize: 18, fontWeight: pw.FontWeight.bold)),
            pw.SizedBox(height: 10),
            pw.Table.fromTextArray(
              context: context,
              data: <List<String>>[
                <String>['Event Name', 'Type', 'Date', 'Location', 'Owner'],
                ...events.map((e) => [
                      e.eventName,
                      e.eventType,
                      e.eventDate.toString().split(' ')[0],
                      e.location,
                      e.ownerName ?? 'Unknown',
                    ]),
              ],
              headerStyle: pw.TextStyle(fontWeight: pw.FontWeight.bold),
              headerDecoration: const pw.BoxDecoration(color: PdfColors.grey300),
            ),
          ];
        },
      ),
    );

    await Printing.layoutPdf(
      onLayout: (PdfPageFormat format) async => pdf.save(),
      name: 'System_Admin_Report.pdf',
    );
  }
}
