Hash table / hash bucket / hash map

Folder file storage limits:.

- Windows FAT32 = 65,534
- Windows NTFS = 4,294,967,295 (unlimited)
- Max files using hash storage:
one hundred and thirty-seven billion,
four hundred and thirty-eight million,
nine hundred and fifty-three thousand,
four hundred and forty

Linux (uname -r to find ext):
- ext2/ext3 = 10,000
- ext4 = unlimited

```
// Test storage of multi files in multi folders
$oStorage = new HashFileStorage();
for($i = 0; $i <= 1000; $i++){
$sTestFilename = uniqid("store_");
$sTestFileText = "This file contains text for file: " . $sTestFilename;
$bSave = $oStorage->file_put_contents($sTestFilename, $sTestFileText);
error_log("Saving HashFileStorage file: " . $sTestFilename . " (" . var_export($bSave, TRUE) . ")");
}
```