import dayjs from "dayjs";

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

export function formatTime(time) {
    return dayjs(time).format("h:mm A");
}
