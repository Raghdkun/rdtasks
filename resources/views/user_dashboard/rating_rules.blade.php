@extends('layouts.app')

@section('title')
    Rating Rules & Guidelines
@endsection

@section('css')
    <style>
        .rating-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #D81619;
        }
        .metric-header {
            background: linear-gradient(135deg, #660609 0%, #D81619 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .score-table {
            background: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .score-row {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
        }
        .score-row:last-child {
            border-bottom: none;
        }
        .score-number {
            background: #D81619;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .score-0 { background: #6c757d; }
        .score-1 { background: #dc3545; }
        .score-2 { background: #fd7e14; }
        .score-3 { background: #ffc107; }
        .score-4 { background: #28a745; }
        .score-5 { background: #20c997; }
        .back-btn {
            background: linear-gradient(135deg, #660609 0%, #D81619 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(216, 22, 25, 0.3);
            color: white;
            text-decoration: none;
        }
        .definition-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Back Button -->
        <a href="{{ route('user-dashboard.index') }}" class="back-btn">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>

        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="rating-card">
                    <div class="text-center">
                        <h1 class="mb-3"><i class="fas fa-star text-warning"></i> Employee Task Rating Metrics</h1>
                        <p class="lead mb-0">Understanding the 0–5 Scale Performance Evaluation System</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating Metrics -->
        <div class="row">
            <!-- Code/Task Quality -->
            <div class="col-lg-6 mb-4">
                <div class="rating-card">
                    <div class="metric-header">
                        <h4 class="mb-0"><i class="fas fa-code mr-2"></i> 1. Code/Task Quality</h4>
                    </div>
                    <div class="definition-box">
                        <strong>Definition:</strong> Assesses the technical quality, correctness, and professionalism of the work delivered. Includes code standards, functionality, and lack of bugs.
                    </div>
                    <div class="score-table">
                        <div class="score-row">
                            <div class="score-number score-5">5</div>
                            <div>Excellent quality — clean, maintainable, bug-free, follows best practices, and meets all acceptance criteria.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-4">4</div>
                            <div>Very good — minor issues or small improvements possible; passes code reviews with minimal comments.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-3">3</div>
                            <div>Good — generally clean and functional, but has noticeable inconsistencies or minor technical flaws.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-2">2</div>
                            <div>Functional but has significant issues like code duplication, inefficient logic, or poor formatting. Needs refactoring.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-1">1</div>
                            <div>Poor quality — buggy, hard to maintain, lacks documentation or deviates from standards.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-0">0</div>
                            <div>No code submitted, or code is broken and cannot be evaluated.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Output -->
            <div class="col-lg-6 mb-4">
                <div class="rating-card">
                    <div class="metric-header">
                        <h4 class="mb-0"><i class="fas fa-shipping-fast mr-2"></i> 2. Delivery Output</h4>
                    </div>
                    <div class="definition-box">
                        <strong>Definition:</strong> Measures the amount and completeness of work delivered relative to expectations.
                    </div>
                    <div class="score-table">
                        <div class="score-row">
                            <div class="score-number score-5">5</div>
                            <div>Delivered 100% or more — complete and includes enhancements or extras.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-4">4</div>
                            <div>95–99% delivered — mostly complete; only minor parts missing or slightly misaligned.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-3">3</div>
                            <div>90–94% delivered — functional but some non-critical elements incomplete.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-2">2</div>
                            <div>75–89% — important components missing or integration gaps.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-1">1</div>
                            <div>50–74% delivered or output not usable as-is.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-0">0</div>
                            <div>No delivery or deliverables completely off-track.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Time Score -->
            <div class="col-lg-6 mb-4">
                <div class="rating-card">
                    <div class="metric-header">
                        <h4 class="mb-0"><i class="fas fa-clock mr-2"></i> 3. Time Score</h4>
                    </div>
                    <div class="definition-box">
                        <strong>Definition:</strong> Measures adherence to the agreed-upon timeline (adjusted for weekends and justified delays).
                    </div>
                    <div class="score-table">
                        <div class="score-row">
                            <div class="score-number score-5">5</div>
                            <div>Finished early (1+ day ahead of schedule).</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-4">4</div>
                            <div>Delivered on deadline.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-3">3</div>
                            <div>1–2 days late.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-2">2</div>
                            <div>3–4 days late.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-1">1</div>
                            <div>5–7 days late.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-0">0</div>
                            <div>8+ days late or not delivered on time without justification.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Collaboration -->
            <div class="col-lg-6 mb-4">
                <div class="rating-card">
                    <div class="metric-header">
                        <h4 class="mb-0"><i class="fas fa-users mr-2"></i> 4. Collaboration</h4>
                    </div>
                    <div class="definition-box">
                        <strong>Definition:</strong> Evaluates communication, support, and teamwork with peers and stakeholders.
                    </div>
                    <div class="score-table">
                        <div class="score-row">
                            <div class="score-number score-5">5</div>
                            <div>Highly collaborative — proactive, helpful, and clear communicator.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-4">4</div>
                            <div>Strong team player — communicates well and participates actively.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-3">3</div>
                            <div>Cooperative — works well when prompted, responds when needed.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-2">2</div>
                            <div>Limited interaction — slow responses or hesitance to engage.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-1">1</div>
                            <div>Poor collaboration — unresponsive, unclear, or disruptive.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-0">0</div>
                            <div>Refused to collaborate or actively obstructed teamwork.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Complexity & Urgency Handling -->
            <div class="col-12 mb-4">
                <div class="rating-card">
                    <div class="metric-header">
                        <h4 class="mb-0"><i class="fas fa-exclamation-triangle mr-2"></i> 5. Complexity & Urgency Handling</h4>
                        <small class="opacity-75">(Optional if task is simple)</small>
                    </div>
                    <div class="definition-box">
                        <strong>Definition:</strong> Assesses how well the employee performs under pressure with complex or time-sensitive tasks.
                    </div>
                    <div class="score-table">
                        <div class="score-row">
                            <div class="score-number score-5">5</div>
                            <div>Excelled under pressure — managed complex/urgent tasks independently and effectively.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-4">4</div>
                            <div>Performed well with minimal support on complex/urgent work.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-3">3</div>
                            <div>Handled moderate pressure with some guidance.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-2">2</div>
                            <div>Needed significant support or showed stress affecting output.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-1">1</div>
                            <div>Struggled significantly — missed key aspects or delivery quality suffered.</div>
                        </div>
                        <div class="score-row">
                            <div class="score-number score-0">0</div>
                            <div>Refused or failed to handle the responsibility.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="row">
            <div class="col-12">
                <div class="rating-card text-center">
                    <h5 class="text-muted mb-3"><i class="fas fa-info-circle mr-2"></i> Important Notes</h5>
                    <p class="mb-2">• All ratings are based on a 0-5 scale for each metric</p>
                    <p class="mb-2">• Complexity & Urgency Handling is optional for simple tasks</p>
                    <p class="mb-0">• Ratings should be fair, consistent, and based on objective criteria</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Add smooth scrolling for better UX
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if( target.length ) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                }
            });
        });
    </script>
@endsection