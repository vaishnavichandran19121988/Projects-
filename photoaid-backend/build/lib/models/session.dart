/// Model representing a photo session between a tourist and a helper
class Session {
  /// Unique identifier for the session
  final int? id;

  /// ID of the user requesting help (tourist)
  final int requesterId;

  /// ID of the user providing help (helper), null until accepted
  final int? helperId;

  /// Current status of the session
  final SessionStatus status;

  /// Location latitude where the session takes place
  final double locationLat;

  /// Location longitude where the session takes place
  final double locationLng;

  /// Optional description or notes for the session
  final String? description;

  /// When the session was created
  final DateTime? createdAt;

  /// When the session was last updated
  final DateTime? updatedAt;

  /// When the session was completed (if applicable)
  final DateTime? completedAt;

  /// Constructor
  Session({
    this.id,
    required this.requesterId,
    this.helperId,
    required this.status,
    required this.locationLat,
    required this.locationLng,
    this.description,
    this.createdAt,
    this.updatedAt,
    this.completedAt,
  });

  /// Create a copy of this Session with the given fields replaced with new values
  Session copyWith({
    int? id,
    int? requesterId,
    int? helperId,
    SessionStatus? status,
    double? locationLat,
    double? locationLng,
    String? description,
    DateTime? createdAt,
    DateTime? updatedAt,
    DateTime? completedAt,
  }) {
    return Session(
      id: id ?? this.id,
      requesterId: requesterId ?? this.requesterId,
      helperId: helperId ?? this.helperId,
      status: status ?? this.status,
      locationLat: locationLat ?? this.locationLat,
      locationLng: locationLng ?? this.locationLng,
      description: description ?? this.description,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      completedAt: completedAt ?? this.completedAt,
    );
  }

  /// Convert Session to JSON
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'requester_id': requesterId,
      'helper_id': helperId,
      'status': status.toString().split('.').last,
      'location_lat': locationLat,
      'location_lng': locationLng,
      'description': description,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'completed_at': completedAt?.toIso8601String(),
    };
  }

  /// Create Session from JSON
  factory Session.fromJson(Map<String, dynamic> json) {
    // Parse the status string to enum
    final statusStr = json['status'] as String;
    final status = SessionStatus.values.firstWhere(
      (e) => e.toString().split('.').last == statusStr,
      orElse: () => SessionStatus.pending,
    );

    return Session(
      id: json['id'] as int?,
      requesterId: json['tourist_id'] as int,
      helperId: json['helper_id'] as int?,
      status: status,
      locationLat: (json['meeting_point_lat'] as num).toDouble(),
      locationLng: (json['meeting_point_lng'] as num).toDouble(),
      description: json['description'] as String?,
      createdAt: json['created_at'] != null
          ? (json['created_at'] is DateTime
              ? json['created_at'] as DateTime
              : DateTime.parse(json['created_at'].toString()))
          : null,
      updatedAt: json['updated_at'] != null
          ? (json['updated_at'] is DateTime
              ? json['updated_at'] as DateTime
              : DateTime.parse(json['updated_at'].toString()))
          : null,
      completedAt: json['completed_at'] != null
          ? (json['completed_at'] is DateTime
              ? json['completed_at'] as DateTime
              : DateTime.parse(json['completed_at'].toString()))
          : null,
    );
  }
}

/// Enum representing the status of a session
enum SessionStatus {
  /// Session has been requested but not yet accepted
  pending,

  /// Session has been accepted by a helper
  accepted,

  /// Session is in progress
  inProgress,

  /// Session has been completed successfully
  completed,

  /// Session was cancelled before completion
  cancelled,

  /// Session request expired without being accepted
  expired,
}
