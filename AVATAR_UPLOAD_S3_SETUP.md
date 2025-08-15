# Avatar Upload with S3 Configuration Guide

## Overview
This guide explains how to configure avatar uploads to work with Amazon S3 storage.

## What Was Fixed

### 1. JavaScript Issues
- Fixed missing click handler for avatar upload button
- Improved error handling and validation
- Added file type and size validation before upload
- Enhanced progress feedback and success messages

### 2. Backend Improvements
- Updated `UserController::uploadAvatar()` method to support multiple storage drivers
- Added proper error handling and logging
- Improved image processing with quality settings
- Added support for S3 and other cloud storage providers

### 3. Storage Helper Updates
- Enhanced `Helper::getFile()` method to support multiple storage drivers
- Added proper URL generation for S3, Wasabi, Backblaze, DigitalOcean Spaces, etc.

## S3 Configuration

### Step 1: Environment Variables
Add these variables to your `.env` file:

```bash
# Set filesystem driver to S3
FILESYSTEM_DRIVER=s3

# AWS S3 Configuration
AWS_ACCESS_KEY_ID=your_access_key_here
AWS_SECRET_ACCESS_KEY=your_secret_key_here
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
AWS_URL=https://your-bucket-name.s3.amazonaws.com
```

### Step 2: S3 Bucket Configuration
1. Create an S3 bucket in your AWS account
2. Set the bucket policy to allow public read access for uploaded files:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicReadGetObject",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::your-bucket-name/*"
        }
    ]
}
```

3. Configure CORS for your bucket:

```json
[
    {
        "AllowedHeaders": ["*"],
        "AllowedMethods": ["GET", "PUT", "POST", "DELETE"],
        "AllowedOrigins": ["*"],
        "ExposeHeaders": []
    }
]
```

### Step 3: IAM User Permissions
Create an IAM user with the following policy:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:GetObject",
                "s3:PutObject",
                "s3:DeleteObject"
            ],
            "Resource": "arn:aws:s3:::your-bucket-name/*"
        },
        {
            "Effect": "Allow",
            "Action": [
                "s3:ListBucket"
            ],
            "Resource": "arn:aws:s3:::your-bucket-name"
        }
    ]
}
```

## Alternative Storage Providers

The system also supports other storage providers. Simply change the `FILESYSTEM_DRIVER` in your `.env` file:

### DigitalOcean Spaces
```bash
FILESYSTEM_DRIVER=dospace
DOS_ACCESS_KEY_ID=your_key
DOS_SECRET_ACCESS_KEY=your_secret
DOS_DEFAULT_REGION=nyc3
DOS_BUCKET=your-space-name
DOS_CDN=false  # Set to true if using CDN
```

### Wasabi
`s using ptrace on it, like a debugger.  

## Local Storage (Default)
To use local storage (public folder), set:
```bash
FILESYSTEM_DRIVER=default
```

## Testing the Upload

1. Clear config cache: `php artisan config:clear`
2. Navigate to your profile page
3. Click the camera icon on your avatar
4. Select an image file
5. The upload should work with progress indication
6. The avatar should update immediately upon success

## Troubleshooting

### Common Issues

1. **403 Forbidden**: Check your S3 bucket policy and IAM permissions
2. **CORS errors**: Ensure CORS is properly configured on your S3 bucket
3. **File not found**: Verify your bucket name and region in the `.env` file
4. **Slow uploads**: Consider using a CDN or choosing a region closer to your users

### Debug Mode
Enable debug mode to see detailed error messages:
```bash
APP_DEBUG=true
```

### Log Files
Check Laravel logs for detailed error information:
```bash
tail -f storage/logs/laravel.log
```

## Dependencies
The following packages are already included in your `composer.json`:
- `league/flysystem-aws-s3-v3` - For S3 support
- `intervention/image` - For image processing

No additional packages need to be installed.
