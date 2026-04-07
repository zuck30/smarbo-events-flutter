import 'package:smarbo_events/domain/entities/contribution.dart';

class ContributionModel extends Contribution {
  const ContributionModel({
    required super.id,
    required super.eventId,
    required super.contributorName,
    super.phoneNumber,
    required super.promisedAmount,
    required super.paidAmount,
    required super.balance,
    required super.status,
    required super.createdAt,
    required super.updatedAt,
  });

  factory ContributionModel.fromJson(Map<String, dynamic> json) {
    return ContributionModel(
      id: json['id'],
      eventId: json['event_id'],
      contributorName: json['contributor_name'],
      phoneNumber: json['phone_number'],
      promisedAmount: (json['promised_amount'] as num).toDouble(),
      paidAmount: (json['paid_amount'] as num).toDouble(),
      balance: (json['balance'] as num).toDouble(),
      status: json['status'] ?? 'pending',
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'event_id': eventId,
      'contributor_name': contributorName,
      'phone_number': phoneNumber,
      'promised_amount': promisedAmount,
      'paid_amount': paidAmount,
    };
  }
}
