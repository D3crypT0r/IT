import time

class OTPRateLimiter:
    def __init__(self, limit=3, interval=60):
        self.limit = limit  # Maximum number of OTP requests allowed
        self.interval = interval  # Time interval in seconds
        self.requests = {}  # Dictionary to store request timestamps

    def check_rate_limit(self, user_id):
        current_time = time.time()

        # Remove expired requests
        self.requests = {user: timestamp for user, timestamp in self.requests.items() if current_time - timestamp <= self.interval}

        # Check if the number of requests exceeds the limit
        if len(self.requests.get(user_id, [])) >= self.limit:
            return False

        # Add the current request timestamp
        self.requests.setdefault(user_id, []).append(current_time)
        return True

# Example usage
limiter = OTPRateLimiter(limit=3, interval=60)

# User 1 attempts to generate an OTP
user_id = "user1"
if limiter.check_rate_limit(user_id):
    print("OTP generated!")
else:
    print("Rate limit exceeded!")

# User 1 makes another OTP request within the interval
if limiter.check_rate_limit(user_id):
    print("OTP generated!")
else:
    print("Rate limit exceeded!")

# Wait for the interval to pass
time.sleep(60)

# User 1 attempts to generate an OTP after the interval
if limiter.check_rate_limit(user_id):
    print("OTP generated!")
else:
    print("Rate limit exceeded!")
