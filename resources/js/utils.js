export function formatName(lName, mName, fName) {
    if (mName == null || mName.length === 0) {
        return `${lName}, ${fName}`;
    }
    return `${lName}, ${fName}, ${mName}`;
}

export function formatSchoolYear(schoolYear) {
    return schoolYear == null
        ? "None"
        : `${schoolYear.start} to ${schoolYear.end}`;
}
