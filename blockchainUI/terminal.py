import subprocess, sys
res = subprocess.check_output(sys.argv[1:])
print(res.decode("utf-8"))