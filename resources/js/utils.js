export function formatName(lName, mName, fName) {
    if (mName == null || mName.length === 0) {
        return `${lName}, ${fName}`;
    }
    return `${lName}, ${fName}, ${mName}`;
}
