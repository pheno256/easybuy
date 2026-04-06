<?php
require_once '../app/config/config.php';
$page_title = 'Size Guide';

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <h1 class="mb-4">Size Guide</h1>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Clothing Size Chart</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Size</th>
                                    <th>Chest (inches)</th>
                                    <th>Waist (inches)</th>
                                    <th>Hips (inches)</th>
                                    <th>International</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>XS</td><td>32-34</td><td>26-28</td><td>34-36</td><td>XXS-XS</td></tr>
                                <tr><td>S</td><td>35-37</td><td>29-31</td><td>37-39</td><td>S</td></tr>
                                <tr><td>M</td><td>38-40</td><td>32-34</td><td>40-42</td><td>M</td></tr>
                                <tr><td>L</td><td>41-43</td><td>35-37</td><td>43-45</td><td>L</td></tr>
                                <tr><td>XL</td><td>44-46</td><td>38-40</td><td>46-48</td><td>XL</td></tr>
                                <tr><td>XXL</td><td>47-49</td><td>41-43</td><td>49-51</td><td>XXL</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Shoe Size Chart</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>UK Size</th>
                                    <th>US Size</th>
                                    <th>EU Size</th>
                                    <th>Foot Length (cm)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>3</td><td>4</td><td>36</td><td>22.5</td></tr>
                                <tr><td>4</td><td>5</td><td>37</td><td>23.5</td></tr>
                                <tr><td>5</td><td>6</td><td>38</td><td>24.1</td></tr>
                                <tr><td>6</td><td>7</td><td>39</td><td>24.8</td></tr>
                                <tr><td>7</td><td>8</td><td>40</td><td>25.4</td></tr>
                                <tr><td>8</td><td>9</td><td>41</td><td>26.0</td></tr>
                                <tr><td>9</td><td>10</td><td>42</td><td>26.7</td></tr>
                                <tr><td>10</td><td>11</td><td>43</td><td>27.3</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>How to Measure</h5>
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <i class="fas fa-tshirt fa-3x text-primary mb-2"></i>
                            <h6>Chest</h6>
                            <p class="small">Measure around the fullest part of your chest, keeping the tape horizontal.</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <i class="fas fa-male fa-3x text-primary mb-2"></i>
                            <h6>Waist</h6>
                            <p class="small">Measure around your natural waistline, keeping the tape snug.</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <i class="fas fa-shoe-prints fa-3x text-primary mb-2"></i>
                            <h6>Foot Length</h6>
                            <p class="small">Stand on a ruler, measure from heel to longest toe.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Note:</strong> Sizes may vary by brand. Please check the product description for specific sizing information. If you're between sizes, we recommend sizing up.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>