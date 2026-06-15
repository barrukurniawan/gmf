<?php
use App\Models\UsersModel;
use App\Models\SystemModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$xin_system = erp_company_settings();
?>

<style>
.ce-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    gap: 60px;
    min-height: 60vh;
    padding: 40px 20px;
}

.ce-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 260px;
    height: 260px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4680ff 0%, #1b5fff 100%);
    color: #fff;
    text-decoration: none;
    font-size: 20px;
    font-weight: 600;
    text-align: center;
    box-shadow: 0 10px 30px rgba(70, 128, 255, 0.3);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 6px solid rgba(255,255,255,0.3);
    cursor: pointer;
}

.ce-btn:hover {
    transform: scale(1.12) translateY(-10px);
    box-shadow: 0 25px 50px rgba(70, 128, 255, 0.5);
    background: linear-gradient(135deg, #5a91ff 0%, #3372ff 100%);
    border-color: rgba(255,255,255,0.6);
}

.ce-btn:active {
    transform: scale(1.05) translateY(-5px);
}

.ce-btn-icon {
    margin-bottom: 18px;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ce-btn-icon svg {
    width: 70px;
    height: 70px;
    display: block;
}

.ce-btn-icon svg path {
    fill: #fff !important;
}

.ce-btn-text {
    line-height: 1.3;
    padding: 0 20px;
}

.ce-card {
    background-color: rgba(255, 255, 255, 0.45) !important;
    border: 1px solid rgba(255, 255, 255, 0.6);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.ce-title {
    text-align: center;
    margin-bottom: 20px;
    font-weight: 600;
    color: #333;
    font-size: 3rem;
}

@media (max-width: 768px) {
    .ce-container {
        gap: 40px;
    }
    .ce-btn {
        width: 200px;
        height: 200px;
        font-size: 16px;
    }
    .ce-btn-icon {
        width: 60px;
        height: 60px;
    }
    .ce-btn-icon svg {
        width: 50px;
        height: 50px;
    }
}
</style>

<div class="card ce-card">
    <div class="card-header" style="background: transparent; border-bottom: none;">
        <h4 class="ce-title">Pick capability evaluation</h4>
    </div>
    <div class="card-body">
        <div class="ce-container">
            <a href="<?= site_url('erp/capability-evaluation/component'); ?>" class="ce-btn">
                <div class="ce-btn-icon">
                    <svg viewBox="0 0 91 91">
                        <g>
                            <g>
                                <path d="M70.385,38.117c-0.097-0.265-0.323-0.462-0.599-0.522c-0.274-0.06-0.563,0.025-0.762,0.225l-17.01,17.005    c-0.3,0.304-0.325,0.783-0.056,1.1L69.93,77.91c1.856,1.829,3.598,2.758,5.179,2.758l0,0c2.135,0,3.848-1.717,4.983-2.851    c3.18-3.191,2.592-5.448,1.776-8.567L70.385,38.117z" fill="#45596B"/>
                                <path d="M35.268,39.158c0.154,0.126,0.34,0.188,0.525,0.188c0.212,0,0.425-0.082,0.585-0.242l16.953-16.957    c0.199-0.198,0.282-0.486,0.223-0.761c-0.061-0.275-0.256-0.501-0.519-0.6L21.956,9.209c-1.256-0.333-2.444-0.646-3.607-0.646    c-1.175,0-2.798,0.275-4.936,2.421c-2.689,2.692-4.72,5.497-0.036,10.246L35.268,39.158z" fill="#45596B"/>
                                <path d="M89.033,2.202c-1.482-1.484-3.347-1.796-4.646-1.796c-2.35,0-4.752,1-6.424,2.676L36.846,44.155    c-0.07,0.046-0.159,0.11-0.203,0.153L21.766,59.155c-0.486,0.481-1.189,0.676-1.858,0.513L5.394,56.037    c-0.553-0.141-0.912-0.199-1.236-0.199c-0.623,0-1.119,0.227-1.594,0.755c-0.057,0.066-0.241,0.256-0.477,0.484    c-1.44,1.396-1.691,1.999-1.746,2.403c-0.044,0.331,0.112,0.654,0.4,0.826L19.256,71.2c0.284,0.169,0.524,0.41,0.695,0.695    l10.904,18.526c0.152,0.257,0.427,0.406,0.713,0.406c0.092,0,0.185-0.016,0.275-0.047c0.358-0.127,1.032-0.481,2.758-2.121    c0.292-0.298,0.417-0.491,0.464-0.568c0.097-0.146,0.146-0.323,0.136-0.503c-0.006-0.104-0.114-0.656-0.226-1.185l-3.444-15.192    c-0.149-0.656,0.046-1.333,0.521-1.812l15.099-15.155c0.031-0.038,0.089-0.143,0.112-0.186L88.067,13.19    C90.891,10.363,91.98,5.153,89.033,2.202z" fill="#647F94"/>
                            </g>
                        </g>
                    </svg>
                </div>
                <span class="ce-btn-text">Aircraft Component</span>
            </a>
            <a href="<?= site_url('erp/capability-evaluation/maintenance'); ?>" class="ce-btn">
                <div class="ce-btn-icon">
                    <svg viewBox="0 0 91 91">
                        <g>
                            <g>
                                <path d="M38.841,55.666l0.682-0.676l-0.02-0.016c-0.881-0.891,4.984-6.855,4.984-6.855l-8.119-8.118L8.663,66.904    c-1.973,1.977-2.92,4.705-2.658,7.686c0.242,2.793,1.533,5.49,3.813,7.771c2.43,2.424,5.553,3.66,8.533,3.66    c2.521,0,4.938-0.889,6.746-2.691l20.258-20.26l-5.111-5.17C39.604,57.252,39.151,56.488,38.841,55.666" fill="#45596B"/>
                                <path d="M87.675,16.767l-1.621-3.891L75.616,23.317c-1.777,1.777-3.336,2.678-4.635,2.678    c-0.992,0-2.006-0.537-3.146-1.674l-0.188-0.184c-1.701-1.701-3.016-3.695,1.037-7.75L79.13,5.942l-3.893-1.621    C67.31,1.019,59.005-1.073,51.952,5.985l-6.457,6.451c-6.553,6.561-8.811,14.01-6.699,21.397l6.51,6.646l5.088-5.088    c0,0,3.248,1.688,5.568,3.182l13.875,14.061c3.398-1.033,6.664-3.059,9.732-6.129l6.441-6.452    C93.073,33.001,90.985,24.692,87.675,16.767" fill="#45596B"/>
                                <path d="M80.097,69.682L54.472,43.714c-0.84-0.855-1.898-1.527-3.148-1.996l-1.51-0.568l-4.578,4.58L21.023,21.518    l2.235-2.237c0.602-0.6,0.9-1.439,0.813-2.283c-0.086-0.846-0.547-1.607-1.258-2.074L9.72,6.321    C8.601,5.587,7.118,5.739,6.169,6.687l-4.482,4.49c-0.945,0.947-1.096,2.428-0.361,3.545l8.6,13.094    c0.465,0.711,1.227,1.174,2.072,1.26c0.096,0.01,0.191,0.014,0.287,0.014c0.746,0,1.465-0.295,1.998-0.826l2.02-2.021    l24.211,24.211l-4.588,4.59l0.572,1.512c0.459,1.207,1.119,2.25,1.965,3.105l25.645,25.984c1.688,1.688,4.006,2.617,6.527,2.617    h0.002c2.994,0,6.018-1.309,8.334-3.627l0.133-0.139c2.057-2.051,3.318-4.678,3.553-7.396    C82.907,74.219,81.993,71.582,80.097,69.682z M75.522,80.99l-0.137,0.145c-1.344,1.344-3.076,2.117-4.75,2.117    c-0.838,0-2.039-0.201-2.979-1.139L42.026,56.141c-0.02-0.02-0.039-0.039-0.059-0.059l8.893-8.895    c0.012,0.014,0.023,0.027,0.035,0.039L76.54,73.211c0.844,0.848,1.246,2.076,1.125,3.455C77.53,78.211,76.784,79.73,75.522,80.99z    " fill="#647F94"/>
                            </g>
                        </g>
                    </svg>
                </div>
                <span class="ce-btn-text">Aircraft Maintenance</span>
            </a>
        </div>
    </div>
</div>
