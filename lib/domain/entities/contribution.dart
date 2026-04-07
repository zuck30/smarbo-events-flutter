import 'package:equatable/equatable.dart';

class Contribution extends Equatable {
  final String id;
  final String eventId;
  final String contributorName;
  final String? phoneNumber;
  final double promisedAmount;
  final double paidAmount;
  final double balance;
  final String status;
  final DateTime createdAt;
  final DateTime updatedAt;

  const Contribution({
    required this.id,
    required this.eventId,
    required this.contributorName,
    this.phoneNumber,
    required this.promisedAmount,
    required this.paidAmount,
    required this.balance,
    required this.status,
    required this.createdAt,
    required this.updatedAt,
  });

  @override
  List<Object?> get props => [
        id, eventId, contributorName, phoneNumber, promisedAmount,
        paidAmount, balance, status, createdAt, updatedAt
      ];
}
