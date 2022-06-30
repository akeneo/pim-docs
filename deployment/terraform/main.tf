# Set the base project
provider "google" {
  project     = var.project_id
}

# Create the website bucket
resource "google_storage_bucket" "website" {
  name          = "website-bucket"
  location      = "EU"
  force_destroy = true

  uniform_bucket_level_access = false

  website {
    main_page_suffix = "index.html"
  }
}

# Give public read access to the bucket
resource "google_storage_bucket_access_control" "website_read" {
  bucket = google_storage_bucket.website.name
  role   = "READER"
  entity = "allUsers"
}
resource "google_storage_default_object_access_control" "website_read" {
  bucket = google_storage_bucket.website.name
  role   = "READER"
  entity = "allUsers"
}

# Reserve an external IP
resource "google_compute_global_address" "website" {
  provider = google
  name     = "website-lb-ip"
}

# Add the bucket as a CDN backend
resource "google_compute_backend_bucket" "website" {
  provider    = google
  name        = "website-backend"
  description = "Contains files needed by the website"
  bucket_name = google_storage_bucket.website.name
  enable_cdn  = true
}

# Create HTTPS certificate
resource "google_compute_managed_ssl_certificate" "website" {
  provider = google
  name     = "website-cert"
  managed {
    domains = ["${var.domain_name}"]
  }
}

# GCP URL MAP
resource "google_compute_url_map" "website" {
  provider        = google
  name            = "website-url-map"
  default_service = google_compute_backend_bucket.website.self_link
}

# GCP target proxy
resource "google_compute_target_https_proxy" "website" {
  provider         = google
  name             = "website-target-proxy"
  url_map          = google_compute_url_map.website.self_link
  ssl_certificates = [google_compute_managed_ssl_certificate.website.self_link]
}

# GCP target proxy
resource "google_compute_target_http_proxy" "website_http_proxy" {
  provider         = google
  name             = "website-target-proxy-http"
  url_map          = google_compute_url_map.website.self_link
}

# GCP forwarding rule
resource "google_compute_global_forwarding_rule" "default" {
  provider              = google
  name                  = "website-forwarding-rule"
  load_balancing_scheme = "EXTERNAL"
  ip_address            = google_compute_global_address.website.address
  ip_protocol           = "TCP"
  port_range            = "443"
  target                = google_compute_target_https_proxy.website.self_link
}

# GCP forwarding rule
resource "google_compute_global_forwarding_rule" "http" {
  provider              = google
  name                  = "website-forwarding-rule-http"
  load_balancing_scheme = "EXTERNAL"
  ip_address            = google_compute_global_address.website.address
  ip_protocol           = "TCP"
  port_range            = "80"
  target                = google_compute_target_http_proxy.website_http_proxy.self_link
}

# Configure the backend in the project state bucket
terraform {
  backend "gcs" {
    bucket = var.state_bucket_name
    prefix = "infra/website"
  }
  required_version = "= 1.1.3"
}

resource "google_project_service" "dns" {
  project                    = var.project_id
  service                    = "dns.googleapis.com"
  disable_dependent_services = true
}

resource "google_dns_managed_zone" "website_dns" {
  name     = "docs-sandbox"
  dns_name = "${var.domain_name}."
  description = "Website DNS zone"
  project     = var.project_id

  depends_on = [
    google_project_service.dns,
  ]
}

resource "google_dns_record_set" "a" {
  name         = "${google_dns_managed_zone.website_dns.dns_name}"
  managed_zone = google_dns_managed_zone.website_dns.name
  type         = "A"
  ttl          = 300
  rrdatas      = ["${google_compute_global_address.website.address}"]
}
